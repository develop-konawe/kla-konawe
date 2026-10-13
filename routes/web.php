<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Public Pages (served via Inertia React) ────────────────────────────────────
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

// Berita / Posts
Route::get('/berita', [\App\Http\Controllers\PostController::class, 'index']);
Route::get('/berita/{slug}', [\App\Http\Controllers\PostController::class, 'show']);

// Publikasi
Route::get('/publikasi', [\App\Http\Controllers\PublicationController::class, 'index']);

// Halaman Statis
Route::get('/tentang-kla', [\App\Http\Controllers\PageController::class, 'show'])->defaults('slug', 'tentang-kla');
Route::get('/halaman/{slug}', [\App\Http\Controllers\PageController::class, 'show']);

// Layanan
Route::get('/layanan', [\App\Http\Controllers\ServiceController::class, 'index']);
Route::get('/layanan/{slug}', [\App\Http\Controllers\ServiceController::class, 'show']);

// Fasilitas
Route::get('/fasilitas', [\App\Http\Controllers\FacilityController::class, 'index']);
Route::get('/fasilitas/{slug}', [\App\Http\Controllers\FacilityController::class, 'show']);

// Pengaduan — sekarang React (bukan Livewire)
Route::get('/pengaduan', fn () => Inertia::render('Complaint'));
Route::get('/cek-pengaduan', fn () => Inertia::render('ComplaintStatus'));

// ── API Routes (used by React frontend) ───────────────────────────────────────
Route::prefix('api')->group(function () {
    Route::post('/pengaduan', [\App\Http\Controllers\Api\ComplaintApiController::class, 'store']);
    Route::get('/pengaduan/{ticketNumber}', [\App\Http\Controllers\Api\ComplaintApiController::class, 'show']);
});

// Fallback redirect for external image URLs accidentally requested via /storage/
Route::get('/storage/{url}', function () {
    $uri = request()->getRequestUri();
    if (preg_match('#^/storage/(https?):/(?:/)?(.+)$#i', $uri, $matches)) {
        return redirect()->away($matches[1] . '://' . $matches[2]);
    }
    abort(404);
})->where('url', '.*')->withoutMiddleware('web');

