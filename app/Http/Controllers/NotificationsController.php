<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function show($id)
    {
        // Mengambil data jenis_dokumen dari tabel peminjamen
        $jenisDokumen = DB::table('peminjamen')->where('id', $id)->value('jenis_dokumen');
        
        // Kirim data ke view atau langsung ke Filament
        return view('peminjamen.show', ['jenisDokumen' => $jenisDokumen]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
