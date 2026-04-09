<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlumniDetail;
use App\Models\Alumni; // <-- Tambahkan model Alumni
use Illuminate\Support\Facades\Http;

class PddiktiController extends Controller
{
    public function index()
    {
        // Ambil daftar alumni (diurutkan berdasarkan nama) untuk dropdown
        $alumniList = Alumni::orderBy('nama_lengkap')->get();
        
        return view('pddikti.index', compact('alumniList'));
    }

    public function search(Request $request)
    {
        // Validasi: sekarang hanya butuh ID alumni & universitas
        $request->validate([
            'alumni_id'   => 'required|exists:alumni,id',
            'universitas' => 'required',
        ]);

        // Tarik data alumni terpilih
        $alumni = Alumni::findOrFail($request->alumni_id);

        // Format query dari data alumni yang dipilih di tabel
        $query = urlencode(
            strtoupper($alumni->nama_lengkap . ' ' . $alumni->program_studi . ' ' . $request->universitas)
        );

        // 🔎 STEP 1: SEARCH
        $searchResponse = Http::get("https://pddikti.fastapicloud.dev/api/search/mhs/{$query}/");

        if (!$searchResponse->successful() || empty($searchResponse->json())) {
            return back()->with('error', "Data untuk {$alumni->nama_lengkap} tidak ditemukan di PDDIKTI. Pastikan data di database sudah benar.");
        }

        $firstResult = $searchResponse->json()[0];
        $id = $firstResult['id'];

        // 🔍 STEP 2: DETAIL
        $detailResponse = Http::get("https://pddikti.fastapicloud.dev/api/mhs/detail/" . urlencode($id) . "/");

        if (!$detailResponse->successful()) {
            return back()->with('error', 'Gagal mengambil detail data dari PDDIKTI.');
        }

        $data = $detailResponse->json();

        // 💾 STEP 3: SIMPAN ATAU UPDATE KE DATABASE
        $alumniDetail = AlumniDetail::updateOrCreate(
            [
                'nim' => $data['nim'] ?? null,
                'kode_pt' => trim($data['kode_pt'] ?? ''),
            ],
            [
                'nama' => $data['nama'] ?? null,
                'nama_pt' => $data['nama_pt'] ?? null,
                'prodi' => $data['prodi'] ?? null,
                'kode_prodi' => $data['kode_prodi'] ?? null,
                'jenjang' => $data['jenjang'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'status' => $data['status_saat_ini'] ?? null,
                'tanggal_masuk' => $data['tanggal_masuk'] ?? null,
            ]
        );

        return redirect()->route('pddikti.index')
            ->with('success', "Data {$alumni->nama_lengkap} berhasil diverifikasi & disimpan/diperbarui di database!")
            ->with('alumni_data', $alumniDetail);
    }
}