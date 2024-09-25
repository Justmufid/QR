@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Detail Pengunjung</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Name:</strong> {{ $visitor->name }}</p>
                <p><strong>Email:</strong> {{ $visitor->email }}</p>
                <p><strong>Affiliation</strong> {{ $visitor->affiliation }}</p>
            </div>
        </div>

        <h2 class="mt-4">QR Code</h2>
        <div class="text-center">
            {!! $qrCode !!}
        </div>

        <a href="{{ route('visitor.index') }}" class="btn btn-primary mt-3">Kembali ke Daftar Pengunjung</a>
    </div>

@endsection
