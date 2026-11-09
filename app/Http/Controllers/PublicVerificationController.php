<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Transkrip;
use Illuminate\View\View;

class PublicVerificationController extends Controller
{
    /**
     * Public verification endpoint for academic transcripts.
     */
    public function verify(string $kode): View
    {
        $transkrip = Transkrip::with([
            'mahasiswa.fakultas',
            'mahasiswa.programStudi',
            'wisudawan',
        ])->where('kode_verifikasi', $kode)->first();

        if ($transkrip) {
            AuditLog::record(
                'Akses Verifikasi Publik',
                'Verifikasi',
                (string) $transkrip->id,
                "Verifikasi kode transkrip: {$kode}"
            );
        }

        return view('public.verification', compact('transkrip', 'kode'));
    }
}
