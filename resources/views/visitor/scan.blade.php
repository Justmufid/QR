<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Scan QR Code</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('LTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('LTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('LTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('LTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        video {
            transform: scaleX(-1);
        }
    </style>
</head>
<body>    

@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>Scan QR Code</h1>       
        <center>
            <div class="form-group">
                <label for="room">Pilih Ruangan:</label>
                <select class="form-control" id="room">
                    <option value="">-- Pilih Ruangan --</option>
                    <option value="1">Ruangan 1</option>
                    <option value="2">Ruangan 2</option>
                    <option value="3">Ruangan 3</option>
                </select>
            </div>
            <div id="qr-reader" style="width: 500px;"></div>
            <div id="qr-reader-results"></div>
            <div class="form-group mt-4">
                <h2>Manual Check-in</h2>
                <label for="id">ID User:</label>
                <input type="text" id="id" class="form-control" placeholder="Masukkan ID">
    
                <label for="name" class="mt-2">Name:</label>
                <input type="text" id="name" class="form-control" placeholder="Masukkan Nama">
    
                <button id="manual-checkin" class="btn btn-primary mt-3">Check-in</button>
            </div>
        </center>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID Conference</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Affiliation</th>
                    <th>Check-in Time</th> 
                    <th>Room</th>
                </tr>
            </thead>
            <tbody id="tabel-visitor">
                @foreach($checkInTimes as $checkIn)
                    <tr>
                        <td>{{ $checkIn->visitor->id_conference }}</td>
                        <td>{{ $checkIn->visitor->name }}</td>
                        <td>{{ $checkIn->visitor->email }}</td>
                        <td>{{ $checkIn->visitor->affiliation }}</td>
                        <td>{{ $checkIn->check_in_time }}</td>
                        <td>{{ $checkIn->room }}</td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        var scanning = false; // Flag untuk mencegah scan berulang

        function onScanSuccess(decodedText, decodedResult) {
            if (scanning) return; // Jika sedang scanning, hentikan proses

            const selectedRoom = document.getElementById('room').value;
            
            // Validasi jika ruangan belum dipilih
            if (!selectedRoom) {
                Swal.fire({
                    title: 'Ruangan belum dipilih',
                    text: 'Silakan pilih ruangan sebelum melakukan check-in.',
                    icon: 'warning',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            console.log('QR Code scanned:', decodedText);
            scanning = true; // Aktifkan flag scanning

            fetch('/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    qr_code: decodedText,
                    room: selectedRoom // Kirim data room
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    document.getElementById('visitor-info').insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${data.visitor.id_conference}</td>
                            <td>${data.visitor.name}</td>
                            <td>${data.visitor.email}</td>
                            <td>${data.visitor.affiliation}</td>
                            <td>${data.visitor.check_in_time}</td>
                            <td>Ruangan ${data.visitor.room}</td>
                        </tr>
                    `);

                    Swal.fire({
                        title: `Welcome Mr./Mrs. ${data.visitor.name}`,
                        text: `You have successfully checked in to Room ${data.visitor.room}.`,
                        icon: 'success',
                        timer: 2500, // Set timer to 2.5 seconds (2500 ms)
                        showConfirmButton: false // Tidak menampilkan tombol OK
                    });
                } else {
                    Swal.fire({
                        title: 'Check-in failed',
                        text: data.message,
                        icon: 'error',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }

                // Tunggu 3 detik sebelum mengizinkan scan ulang
                setTimeout(() => {
                    scanning = false; // Izinkan scan lagi setelah 3 detik
                }, 3000);
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    timer: 2500,
                    showConfirmButton: false
                });

                // Tetap izinkan scan lagi setelah error, setelah 3 detik
                setTimeout(() => {
                    scanning = false;
                }, 3000);
            });
        }

        // Mulai proses QR code scanning
        var html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).catch(err => {
            console.error('Unable to start QR code scanner', err);
        });

        document.getElementById('manual-checkin').addEventListener('click', function() {
            const idVisitor = document.getElementById('id').value;
            const name = document.getElementById('name').value;
            const selectedRoom = document.getElementById('room').value;

            // Validasi ruangan dan input form
            if (!selectedRoom) {
                Swal.fire({
                    title: 'Ruangan belum dipilih',
                    text: 'Silakan pilih ruangan sebelum melakukan check-in.',
                    icon: 'warning',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }
            
            if (!idVisitor || !name) {
                Swal.fire({
                    title: 'Data belum lengkap',
                    text: 'Silakan masukkan ID dan Nama.',
                    icon: 'warning',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            fetch('/getForm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: idVisitor,
                    name: name,
                    room: selectedRoom,
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response Data:', data);
                if (data.success) {
                    document.getElementById('tabel-visitor').insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${data.visitor.id}</td>
                            <td>${data.visitor.name}</td>
                            <td>${data.visitor.email}</td>
                            <td>${data.visitor.affiliation}</td>
                            <td>${data.visitor.check_in_time}</td>
                            <td>Ruangan ${data.visitor.room}</td>
                        </tr>
                    `);

                    Swal.fire({
                        title: `Welcome Mr./Mrs. ${data.visitor.name}`,
                        text: `You have successfully checked in to Room ${data.visitor.room}.`,
                        icon: 'success',
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    console.log('Error Message:', data.message);
                    Swal.fire({
                        title: 'Check-in failed',
                        text: data.message,
                        icon: 'error',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                console.error('Fetch error:', err); // Tambahkan ini untuk log kesalahan
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        });
    </script>
@endsection
