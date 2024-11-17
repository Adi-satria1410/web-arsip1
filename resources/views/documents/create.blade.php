@extends('layouts.app')

@section('content')
<div class="container my-5 p-4 shadow-lg bg-light rounded" style="max-width: 600px; animation: fadeIn 1s;">
    <div class="bg-primary text-white text-center py-3 rounded-top">
        <h3>Formulir Peminjaman Dokumen</h3>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('documents.store') }}" class="mt-3" method="POST" id="documentForm">
        @csrf

        <!-- Progress Bar -->
        <div class="progress mb-4" style="height: 5px;">
            <div class="progress-bar bg-success" id="progressBar" role="progressbar" style="width: 0%;"
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">
                <i class="bi bi-file-earmark"></i> Dokumen Dipinjam:
            </label>
            <select class="select2-multiple form-control" id="jenis_dokumen" multiple="multiple" name="jenis_dokumen[]">
                <option value="Warkah">Warkah</option>
                <option value="BT">Buku Tanah</option>
                <option value="SU">Surat Ukur</option>
            </select>
        </div>



        <div class="form-group mb-3">
            <label class="form-label">
                <i class="bi bi-hash"></i> Jenis & No. Hak/DI 208
            </label>
            <input type="text" name="jenis_no_hak_di_208" class="form-control input-animated"
                placeholder="Masukkan Jenis & No. Hak/DI 208" required>
        </div>


        <div class="form-group mb-3">
            <label class="form-label">
                <i class="bi bi-card-text"></i> Nomor Dokumen
            </label>
            <input type="text" name="nomor_dokumen" class="form-control input-animated"
                placeholder="Masukkan Nomor Dokumen" required>
        </div>

        <div class="form-group mb-3">
            <label for="regency" class="form-label"><i class="bi bi-geo-alt"></i> Kabupaten:</label>
            <select name="regency_id" id="regency" class="form-select input-animated" required>
                <option value="" disabled selected>Pilih Kabupaten</option>
                @foreach($regencies as $regency)
                    <option value="{{ $regency->id }}">{{ $regency->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="district" class="form-label"><i class="bi bi-map"></i> Kecamatan:</label>
            <select name="district_id" id="district" class="form-select input-animated" required>
                <option value="" disabled selected>Pilih Kecamatan</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="village" class="form-label"> <i class="bi bi-building"></i> Desa:</label>
            <select name="village_id" id="village" class="form-select input-animated" required>
                <option value="" disabled selected>Pilih Desa</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label"><i class="bi bi-person"></i> Peminjam:</label>
            <input type="text" name="peminjam" class="form-control input-animated" placeholder="Masukkan Nama Peminjam"
                required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label"><i class="bi bi-clipboard"></i> Keperluan:</label>
            <textarea type="text" name="keperluan" class="form-control input-animated" rows="3"
                placeholder="Masukkan keperluan peminjaman dokumen" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label class="form-label"><i class="bi bi-calendar"></i> Tanggal Peminjaman:</label>
            <input type="date" name="tanggal_peminjaman" class="form-control input-animated" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label"><i class="bi bi-calendar-check"></i> Tanggal Pengembalian:</label>
            <input type="date" name="tanggal_pengembalian" class="form-control input-animated" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label"><i class="bi bi-file-earmark"></i> DI 301:</label>
            <input type="text" name="di_301" class="form-control input-animated">
        </div>

        <div class="form-group mb-4">
            <label class="form-label"><i class="bi bi-file-earmark"></i> DI 302/303:</label>
            <input type="text" name="di_302_303" class="form-control input-animated">
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button id="submitButton" type="submit" class="btn btn-success px-4 py-2"> <i
                    class="bi bi-cloud-arrow-up"></i> Submit</button>
        </div>
    </form>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-animated:focus {
        border-color: #198754 !important;
        box-shadow: 0 0 10px rgba(25, 135, 84, 0.5);
        transition: box-shadow 0.3s ease-in-out;
    }
</style>
<!-- Tambahkan Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Tambahkan Select2 dan Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('.select2-multiple').select2({
            placeholder: "Pilih jenis dokumen",
            allowClear: true,
            width: '100%'
        });

        // Select Kabupaten
        $('#regency').on('change', function () {
            var regencyId = $(this).val();
            $('#district').empty().append('<option value="" disabled selected>Loading...</option>');
            $('#village').empty().append('<option value="" disabled selected>Pilih Desa</option>');

            if (regencyId) {
                $.get('/districts/' + regencyId, function (data) {
                    $('#district').empty().append('<option value="" disabled selected>Pilih Kecamatan</option>');
                    $.each(data, function (id, name) {
                        $('#district').append(new Option(name, id));
                    });
                });
            }
        });

        // AJAX untuk memuat Desa berdasarkan Kecamatan
        $('#district').on('change', function () {
            var districtId = $(this).val();
            $('#village').empty().append('<option value="" disabled selected>Loading...</option>');

            if (districtId) {
                $.get('/villages/' + districtId, function (data) {
                    $('#village').empty().append('<option value="" disabled selected>Pilih Desa</option>');
                    $.each(data, function (id, name) {
                        $('#village').append(new Option(name, id));
                    });
                }).fail(function () {
                    alert('Gagal memuat data desa. Silakan coba lagi.');
                });
            }
        });

        // Ketika memilih jenis dokumen
        $('#jenis_dokumen').on('change', function () {
            var selectedDocuments = $(this).val(); // Mendapatkan jenis dokumen yang dipilih
            var noHakInput = $('input[name="jenis_no_hak_di_208"]');
            var nomorDokumenInput = $('input[name="nomor_dokumen"]');

            if (selectedDocuments && selectedDocuments.includes('Warkah')) {
                // Jika "Warkah" dipilih, tampilkan input nomor hak di 208 dan set nilainya menjadi "208"
                noHakInput.closest('.form-group').show(); // Menampilkan input
                noHakInput.val('208'); // Menetapkan nilai "208"
            } else {
                // Jika selain "Warkah" dipilih, sembunyikan input nomor hak di 208 dan kosongkan nilainya
                noHakInput.closest('.form-group').hide(); // Menyembunyikan input
                noHakInput.val(''); // Mengosongkan nilai
            }

           
        });


        // Animasi Progress Bar
        $('form input, form select').on('input', function () {
            let filled = $('form input:valid, form select:valid').length;
            let total = $('form input, form select').length;
            let progress = (filled / total) * 100;
            $('#progressBar').css('width', progress + '%');
        });

        // Tombol Loading
        $('#submitButton').on('click', function (e) {
            e.preventDefault();  // Menghindari pengiriman form langsung
            $(this).html('<i class="bi bi-hourglass-split"></i> Loading...').attr('disabled', true);
            $('#documentForm').submit();  // Kirimkan form setelah modifikasi
        });
    });
    console.log($('#jenis_dokumen').val());  // Cek nilai jenis dokumen
console.log($('#regency').val());  // Cek nilai kabupaten
console.log($('#district').val());  // Cek nilai kecamatan
console.log($('#village').val());  // Cek nilai desa

</script>

@endsection