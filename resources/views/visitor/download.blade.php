@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>Download Data Pengunjung</h1>

        <!-- Form untuk filter dan download PDF -->
        <form id="download-form" method="POST" action="{{ route('visitor.downloadPdf') }}">
            @csrf
            <div class="form-group">
                <label for="date">Tanggal:</label>
                <input type="date" class="form-control" name="date" id="date">
            </div>
            <div class="form-group">
                <label for="room">Ruangan:</label>
                <select class="form-control" name="room" id="room">
                    <option value="">-- Pilih Ruangan --</option>
                    <option value="1">Ruangan 1</option>
                    <option value="2">Ruangan 2</option>
                    <option value="3">Ruangan 3</option>
                </select>
            </div>
            <p></p>
            <button type="submit" class="btn btn-primary">Download PDF</button>
        </form>
        <a href="{{ route('visitor.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Pengunjung</a>
    </div>
@endsection
