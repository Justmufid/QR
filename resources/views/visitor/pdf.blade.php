<!DOCTYPE html>
<html>
<head>
    <title>Check-in Data</title>
    <link rel="stylesheet" href="{{ public_path('LTE/plugins/bootstrap/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container">
        <h1>Check-in Data</h1>
        <table class="table">
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
            <tbody>
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
</body>
</html>
