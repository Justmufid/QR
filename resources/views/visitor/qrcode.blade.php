@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">QR Code untuk {{ $visitor->name }}</h1>

        <div class="text-center mb-4">
            <!-- Pastikan $visitor->qr_code berisi HTML atau gambar QR Code -->
            {!! $visitor->qr_code !!}
        </div>

        <a href="{{ route('visitor.show', $visitor->id) }}" class="btn btn-secondary">Lihat Detail</a>
    </div>
@endsection
