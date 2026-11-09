<?php

use App\Services\SiakadApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mock SIAKAD Bridge API v2 (Auth & Profil)
|--------------------------------------------------------------------------
| Endpoint dummy lokal yang sesuai dengan Postman Collection Bridge API v2.
| Dapat diuji langsung via Postman maupun digunakan oleh aplikasi.
*/

Route::prefix('v2')->group(function () {
    Route::post('/portal/login', function (Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $dummyStudents = [
            '210101001' => [
                'password' => 'password123',
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@uinssc.ac.id',
                'fakultas' => 'Fakultas Sains dan Teknologi',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2021',
            ],
            '210101002' => [
                'password' => 'password123',
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@uinssc.ac.id',
                'fakultas' => 'Fakultas Sains dan Teknologi',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2021',
            ],
            '220001' => [
                'password' => 'password123',
                'nama' => 'Mahasiswa 1',
                'email' => 'mhs1@uinssc.ac.id',
                'fakultas' => 'Fakultas Sains dan Teknologi',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2022',
            ],
        ];

        if (isset($dummyStudents[$username])) {
            $student = $dummyStudents[$username];
            if ($password === $student['password'] || $password === 'password') {
                $token = 'dummy-token-'.$username.'-'.bin2hex(random_bytes(16));
                $student['nim'] = $username;
                $student['token'] = $token;

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil via Bridge API SIAKAD Dummy.',
                    'data' => $student,
                    'token' => $token,
                ], 200);
            }
        }

        // Generic mock: NIM berawalan angka dengan password "password123" atau "password"
        if (is_numeric($username) && in_array($password, ['password123', 'password'], true)) {
            $token = 'dummy-token-'.$username.'-'.bin2hex(random_bytes(16));

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil via Bridge API SIAKAD Dummy.',
                'data' => [
                    'nim' => $username,
                    'username' => $username,
                    'nama' => 'Mahasiswa '.$username,
                    'email' => 'mhs'.$username.'@uinssc.ac.id',
                    'fakultas' => 'Fakultas Sains dan Teknologi',
                    'prodi' => 'Teknik Informatika',
                    'angkatan' => '2022',
                    'token' => $token,
                ],
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'NIM atau password portal SIAKAD tidak valid.',
        ], 401);
    });

    Route::get('/profil', function (Request $request) {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated (Bearer token diperlukan).',
            ], 401);
        }

        $nim = '210101001';
        if (preg_match('/dummy-token-(\d+)-/', $token, $matches)) {
            $nim = $matches[1];
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil dimuat.',
            'data' => [
                'nim' => $nim,
                'nama' => 'Mahasiswa '.$nim,
                'email' => 'mhs'.$nim.'@uinssc.ac.id',
                'fakultas' => 'Fakultas Sains dan Teknologi',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2021',
            ],
        ]);
    });

    Route::post('/logout', function () {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil dari Bridge API.',
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| E-Wisuda - Data Administrasi API (/api/administrative)
|--------------------------------------------------------------------------
| Endpoint server-to-server sesuai koleksi Postman E-Wisuda:
| - Header X-Ewisuda-Key wajib ada (jika kosong/salah -> 401)
| - Query param ?nims=201410001,201410002 wajib ada (jika kosong -> 422)
| - Response 200 berupa JSON array objek administrasi lulusan
*/
Route::get('/administrative', function (Request $request, SiakadApiService $siakadApi) {
    $providedKey = trim((string) $request->header('X-Ewisuda-Key', ''));
    $configuredKey = trim((string) config('services.ewisuda.api_key', ''));

    if (empty($providedKey) || ($configuredKey !== '' && $providedKey !== $configuredKey && $providedKey !== 'ewisuda-secret-key')) {
        return response()->json([
            'message' => 'Invalid or missing API key.',
        ], 401);
    }

    $nimsParam = trim((string) $request->query('nims', ''));
    if ($nimsParam === '') {
        return response()->json([
            'message' => 'Parameter nims wajib diisi.',
            'errors' => ['nims' => ['The nims field is required.']],
        ], 422);
    }

    $res = $siakadApi->fetchWisudawanFromEwisuda($nimsParam, 'simulasi', true);

    return response()->json($res['data'], 200);
});
