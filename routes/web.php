<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/visitors', [VisitorController::class, 'index'])->name('visitor.index'); // Route untuk menampilkan daftar pengunjung
Route::get('/visitors/create', [VisitorController::class, 'create'])->name('visitor.create'); // Route untuk menampilkan form pendaftaran pengunjung
Route::post('/visitors', [VisitorController::class, 'store'])->name('visitor.store'); // Route untuk menyimpan data pengunjung
Route::get('/visitors/{id}', [VisitorController::class, 'show'])->name('visitor.show'); // Route untuk menampilkan detail pengunjung
Route::get('/visitors/{id}/qr', [VisitorController::class, 'showQr'])->name('visitor.showQr'); // Route untuk menampilkan QR code pengunjung
Route::get('/visitor/undangan/{id}', [VisitorController::class, 'invitation'])->name('visitor.undangan');
Route::get('/visitor/scan', [VisitorController::class, 'showScanPage'])->name('visitor.scan');
Route::post('/check-in', [VisitorController::class, 'checkIn']);
Route::post('/getForm', [VisitorController::class, 'getForm']);
Route::get('visitor/{id}/download-qr-code', [VisitorController::class, 'downloadQrCode'])->name('visitor.downloadQrCode');
Route::post('/download-pdf', [VisitorController::class, 'downloadPdf'])->name('download.pdf');
Route::post('/visitors/download-pdf', [VisitorController::class, 'downloadPdf'])->name('visitor.downloadPdf');
Route::get('/scan', [VisitorController::class, 'showScanPage'])->name('visitor.scan');
Route::post('/download', [VisitorController::class, 'download']);
Route::get('/visitor/download', [VisitorController::class, 'download'])->name('visitor.download');


