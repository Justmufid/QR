@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Undangan untuk {{ $visitor->name }}</h1>

        <!-- Card for Invitation -->
        <div class="card mb-4">
            <div class="card-body">
                <!-- Ucapan Pembuka -->
                <p>Kepada Yth. {{ $visitor->name }}</p>
                <p>Dengan hormat,</p>
                <p>Kami dengan senang hati mengundang Anda untuk menghadiri acara kami yang akan datang. Berikut adalah
                    detail undangan Anda:</p>

                <!-- Data Pengunjung -->
                <hr>
                <p><strong>Name:</strong> {{ $visitor->name }}</p>
                <p><strong>Email:</strong> {{ $visitor->email }}</p>
                <p><strong>Affiliation:</strong> {{ $visitor->affiliation }}</p>
                <p><strong>QR Code:</strong></p>
                <div class="text-center">
                    {!! $qrCode !!}
                </div>

                <!-- Penutup -->
                <hr>
                <p>Terima kasih atas perhatian Anda. Kami berharap dapat bertemu dengan Anda di acara kami.</p>
                <p>Salam hormat,</p>
                <p>Tim Acara</p>
            </div>
        </div>

        <!-- Tombol untuk Salin Link dan Unduh HTML -->
        <div class="mb-3">
            <a href="{{ route('visitor.index') }}" class="btn btn-secondary no-print">Kembali ke Daftar Pengunjung</a>
            <a id="downloadPdfButton" href="{{ route('visitor.downloadPDF', $visitor->id) }}"
                class="btn btn-success no-print">Unduh PDF</a>
            <a id="downloadButton" href="{{ route('visitor.downloadInvitation', $visitor->id) }}"
                class="btn btn-success no-print">Unduh HTML</a>
        </div>

    </div>
@endsection
