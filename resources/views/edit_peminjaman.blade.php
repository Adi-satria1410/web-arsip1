@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Peminjaman</h1>

    <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="jenis_dokumen">Jenis Dokumen</label>
            <input type="text" name="jenis_dokumen" id="jenis_dokumen" class="form-control" value="{{ $peminjaman->jenis_dokumen }}" required>
        </div>

        <div class="form-group">
            <label for="nomor_dokumen">Nomor Dokumen</label>
            <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="form-control" value="{{ $peminjaman->nomor_dokumen }}" required>
        </div>

        <div class="form-group">
            <label for="peminjam">Nama Peminjam</label>
            <input type="text" name="peminjam" id="peminjam" class="form-control" value="{{ $peminjaman->peminjam }}" required>
        </div>

        <div class="form-group">
            <label for="tanggal_peminjaman">Tanggal Peminjaman</label>
            <input type="date" name="tanggal_peminjaman" id="tanggal_peminjaman" class="form-control" value="{{ $peminjaman->tanggal_peminjaman->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="tanggal_pengembalian">Tanggal Pengembalian</label>
            <input type="date" name="tanggal_pengembalian" id="tanggal_pengembalian" class="form-control" value="{{ $peminjaman->tanggal_pengembalian ? $peminjaman->tanggal_pengembalian->format('Y-m-d') : '' }}">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="Dipinjam" {{ $peminjaman->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Dikembalikan" {{ $peminjaman->status == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="denda">Denda</label>
            <input type="number" name="denda" id="denda" class="form-control" value="{{ $peminjaman->denda }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Peminjaman</button>
    </form>
</div>
@endsection
