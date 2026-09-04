<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
Route::get('/berita', [\App\Http\Controllers\PostController::class, 'index']);
Route::get('/berita/{slug}', [\App\Http\Controllers\PostController::class, 'show']);
Route::get('/publikasi', [\App\Http\Controllers\PublicationController::class, 'index']);
Route::get('/tentang-kla', [\App\Http\Controllers\PageController::class, 'show'])->defaults('slug', 'tentang-kla');
Route::get('/halaman/{slug}', [\App\Http\Controllers\PageController::class, 'show']);
Route::get('/layanan', [\App\Http\Controllers\ServiceController::class, 'index']);
Route::get('/layanan/{slug}', [\App\Http\Controllers\ServiceController::class, 'show']);
Route::get('/fasilitas', [\App\Http\Controllers\FacilityController::class, 'index']);
Route::get('/fasilitas/{slug}', [\App\Http\Controllers\FacilityController::class, 'show']);

// Livewire Routes
Route::get('/pengaduan', \App\Livewire\ComplaintForm::class);
Route::get('/cek-pengaduan', \App\Livewire\ComplaintStatus::class);
