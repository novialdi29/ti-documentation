<?php

use App\Http\Controllers\DokumentasiPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dokumentasi/{dokumentasi}/export-pdf', [DokumentasiPdfController::class, 'show'])
        ->name('dokumentasi.export-pdf');
    Route::get('/dokumentasi/export/filtered-pdf', [DokumentasiPdfController::class, 'exportFiltered'])
        ->name('dokumentasi.export-filtered-pdf');
});
