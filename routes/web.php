<?php

use App\Filament\Resources\PeminjamanResource;
use App\Http\Controllers\PeminjamanController;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\DocumentController;
use Filament\Facades\Filament;

// User document routes
Route::get('/', [DocumentController::class, 'create'])->name('documents.create'); // Menggunakan GET untuk menampilkan formulir
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store'); // POST untuk menyimpan data
Route::get('/districts/{regencyId}', [DocumentController::class, 'getDistricts']); // GET untuk mengambil data kecamatan
Route::get('/villages/{districtId}', [DocumentController::class, 'getVillages']); // GET untuk mengambil data desa


// Peminjaman resource routes with authentication
Route::middleware(['auth', 'verified'])->group(function () {
    // Register Filament Resources
    Filament::registerResources([
        PeminjamanResource::class,
    ]);
    
    // Peminjaman specific routes for reports, export, etc.
   

    // CRUD routes for peminjaman
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::get('/peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
});

// Route for generating PDF based on peminjaman id
Route::get('/peminjaman/{id}/pdf', [PeminjamanController::class, 'generatePdf'])->name('peminjaman.pdf');
