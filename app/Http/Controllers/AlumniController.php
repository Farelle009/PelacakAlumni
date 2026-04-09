<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlumniController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $alumni = Alumni::query()
            ->select('alumni.*')
            // Cek apakah data alumni ada di tabel alumni_details berdasarkan nama & nim
            // Menggunakan LOWER dan TRIM untuk menghindari error huruf besar/kecil & spasi berlebih
            ->selectSub(function ($query) {
                $query->selectRaw('1')
                      ->from('alumni_details')
                      ->whereRaw('LOWER(TRIM(alumni_details.nim)) = LOWER(TRIM(alumni.nim))')
                      ->whereRaw('LOWER(TRIM(alumni_details.nama)) = LOWER(TRIM(alumni.nama_lengkap))')
                      ->limit(1);
            }, 'is_pddikti_verified')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('program_studi', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status_pelacakan', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('alumni.index', [
            'alumni'        => $alumni,
            'search'        => $search,
            'status'        => $status,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function create(): View
    {
        return view('alumni.create', [
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        Alumni::create($validated);

        return redirect()
            ->route('alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function show(Alumni $alumni): View
    {
        $alumni->load(['trackingResults.trackingSource']);

        return view('alumni.show', compact('alumni'));
    }

    public function edit(Alumni $alumni): View
    {
        return view('alumni.edit', [
            'alumni'        => $alumni,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, Alumni $alumni): RedirectResponse
    {
        $validated = $this->validateRequest($request, $alumni->id);

        $alumni->update($validated);

        return redirect()
            ->route('alumni.show', $alumni)
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni): RedirectResponse
    {
        $alumni->delete();

        return redirect()
            ->route('alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function statusOptions(): array
    {
        return [
            Alumni::STATUS_BELUM_DILACAK,
            Alumni::STATUS_TERIDENTIFIKASI,
            Alumni::STATUS_PERLU_VERIFIKASI,
            Alumni::STATUS_TIDAK_DITEMUKAN,
        ];
    }

    protected function validateRequest(Request $request, ?int $alumniId = null): array
    {
        return $request->validate([
            'nim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('alumni', 'nim')->ignore($alumniId),
            ],
            'nama_lengkap'    => ['required', 'string', 'max:255'],
            'program_studi'   => ['required', 'string', 'max:255'],
            'tahun_lulus'     => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'email'           => ['nullable', 'email', 'max:255'],
            'kota'            => ['nullable', 'string', 'max:255'],
            'status_pelacakan' => [
                'required',
                Rule::in($this->statusOptions()),
            ],
        ]);
    }
}