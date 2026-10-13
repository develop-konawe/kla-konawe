<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ComplaintApiController extends Controller
{
    /**
     * Submit a new complaint via API (called from React form).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string|min:10',
            'is_anonymous' => 'required|string|in:true,false',
            'reporter_name'  => 'nullable|string|max:255',
            'reporter_email' => 'nullable|email|max:255',
            'reporter_phone' => 'nullable|string|max:255',
            'attachments'  => 'nullable|file|max:5120',
        ]);

        try {
            $apiUrl = env('PENGADUAN_API_URL', 'http://127.0.0.1:9000/api/v1/complaints');

            $http = Http::asMultipart();

            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $http = $http->attach(
                    'attachments',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            $response = $http->post($apiUrl, $request->except('attachments'));

            if ($response->successful() && $response->json('success')) {
                return response()->json([
                    'success' => true,
                    'data'    => $response->json('data'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengaduan. Pastikan server API tersedia.',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi saat mengirim pengaduan.',
            ], 500);
        }
    }

    /**
     * Check complaint status by ticket number.
     */
    public function show(string $ticketNumber)
    {
        try {
            $apiUrl = env('PENGADUAN_API_URL', 'http://127.0.0.1:9000/api/v1/complaints');
            $response = Http::get("{$apiUrl}/{$ticketNumber}");

            if ($response->successful() && $response->json('success')) {
                return response()->json([
                    'success' => true,
                    'data'    => $response->json('data'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nomor tiket tidak ditemukan.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server.',
            ], 500);
        }
    }
}
