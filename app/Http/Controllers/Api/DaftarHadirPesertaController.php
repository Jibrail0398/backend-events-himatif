<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaftarHadirPeserta;
use Illuminate\Http\Request;
use Carbon\Carbon;


class DaftarHadirPesertaController extends Controller
{
    /**
     * Scan QR peserta untuk presensi otomatis.
     *
     * @param int $id ID DaftarHadirPeserta
     */
    
    public function scanPresensi($id)
    {
        $daftar = DaftarHadirPeserta::with('penerimaanPeserta.pendaftarPeserta.event')->find($id);

        if (!$daftar) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan',
            ], 404);
        }

        $peserta = $daftar->penerimaanPeserta->pendaftarPeserta;
        $event   = $peserta->event;

        // Cek status pembayaran
        if ($daftar->penerimaanPeserta->status_pembayaran !== 'lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Peserta belum lunas, tidak bisa presensi',
                'data'    => $peserta,
            ], 403);
        }

        $now = Carbon::now();

        // Jika belum presensi datang
        if ($daftar->presensi_datang !== 'hadir') {
            $daftar->update([
                'presensi_datang' => 'hadir',
                'waktu_presensi_datang' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi datang berhasil dicatat',
                'data'    => [
                    'peserta' => $peserta,
                    'event'   => $event,
                    'presensi' => $daftar,
                    'waktu'   => $now->toDateTimeString(),
                ]
            ], 200);
        }

        // Jika sudah datang tapi belum pulang
        if ($daftar->presensi_pulang !== 'pulang') {
            $daftar->update([
                'presensi_pulang' => 'pulang',
                'waktu_presensi_pulang' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi pulang berhasil dicatat',
                'data'    => [
                    'peserta' => $peserta,
                    'event'   => $event,
                    'presensi' => $daftar,
                    'waktu'   => $now->toDateTimeString(),
                ]
            ], 200);
        }

        // Sudah datang dan pulang
        return response()->json([
            'success' => false,
            'message' => 'Peserta sudah lengkap presensinya',
            'data'    => [
                'peserta' => $peserta,
                'event'   => $event,
                'presensi' => $daftar,
            ]
        ], 400);
    }

    public function index(){
        $data = DaftarHadirPeserta::with([
            'penerimaanPanitia.pendaftarPanitia:id,kode_panitia,nama,NIM,email,divisi',
            'penerimaanPanitia.pendaftarPanitia.event:id,nama_event,kode_event'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(function($item) {
                return [
                    'id' => $item->id,
                    'presensi_datang' => $item->presensi_datang,
                    'waktu_presensi_datang' => $item->waktu_presensi_datang,
                    'presensi_pulang' => $item->presensi_pulang,
                    'waktu_presensi_pulang' => $item->waktu_presensi_pulang,
                    'peserta' => $item->penerimaanPeserta->pendaftarPeserta ?? null,
                    'event' => $item->penerimaanPeserta->pendaftarPeserta->event ?? null
                ];
            })
        ]);
    }

}