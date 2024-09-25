<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CheckInTime;
use Barryvdh\DomPDF\Facade as PDF;


class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visitor::all();
        $checkInTimes = CheckInTime::with('visitor')->whereDate('check_in_time', Carbon::today())->get();

        return view('visitor.index', compact('visitors', 'checkInTimes'));
    }

    public function create()
    {
        return view('visitor.create');
    }

    public function download()
    {
        // Mengambil semua data visitors
        $visitors = Visitor::all();
        
        // Mengirim data ke view visitor.download
        return view('visitor.download', compact('visitors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'affiliation' => 'nullable|string|max:255',
        ]);

        $visitor = Visitor::create([
            'id_conference' => $request->id_conference,
            'name' => $request->name,
            'email' => $request->email,
            'affiliation' => $request->affiliation,
        ]);

        $qrCodeUrl = route('visitor.show', $visitor->id);

        Log::info('QR Code URL generated: ' . $qrCodeUrl);

        $visitor->qr_code = $qrCodeUrl;
        $visitor->save();

        return redirect()->route('visitor.index')->with('success', 'Visitor added successfully.');
    }

    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);

        $qrCode = QrCode::size(200)->generate(route('visitor.show', $visitor->id));

        return view('visitor.show', compact('visitor', 'qrCode'));
    }

    public function scan(Request $request)
    {
        $visitor = Visitor::where('id', $request->input('id'))
                    ->where('attended', false)
                    ->first();

        if ($visitor) {
            $visitor->attended = true;
            $visitor->save();
            return response()->json(['status' => 'success', 'message' => 'Attendance marked.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid or already marked as attended.']);
    }

    public function invitation($id)
    {
        $visitor = Visitor::findOrFail($id);
        $qrCode = QrCode::size(250)->generate(route('visitor.undangan', $id));
        return view('visitor.undangan', compact('visitor', 'qrCode'));
    }

public function showScanPage()
{
    $checkInTimes = CheckInTime::with('visitor')->get(); // Ambil semua data check-in
    return view('visitor.scan', compact('checkInTimes'));
}

    public function checkIn(Request $request)
    {
        Log::info('Check-in process started.');
    
        $request->validate([
            'qr_code' => 'required|string',
            'room' => 'required|string',
        ]);
    
        Log::info('QR Code received: ' . $request->qr_code);
        Log::info('Room received: ' . $request->room);
    
        $visitor = Visitor::where('qr_code', $request->qr_code)->first();
    
        if ($visitor) {
            $today = Carbon::now()->format('Y-m-d');
    
            $checkInToday = CheckInTime::where('visitor_id', $visitor->id)
                ->whereDate('check_in_time', Carbon::today())
                ->exists();
    
            if ($checkInToday) {
                Log::warning('Visitor has already checked in today: ' . $visitor->id);
                return response()->json(['success' => false, 'message' => 'You have already checked in today.']);
            }
    
            Log::info('Saving CheckInTime for Visitor ID: ' . $visitor->id);
    
            $checkInTime = new CheckInTime();
            $checkInTime->visitor_id = $visitor->id;
            $checkInTime->check_in_time = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
            $checkInTime->room = $request->room;
    
            Log::info('Data to be saved:', [
                'visitor_id' => $visitor->id,
                'check_in_time' => $checkInTime->check_in_time,
                'room' => $checkInTime->room,
            ]);
    
            $checkInTime->save();
    
            Log::info('Visitor checked in successfully: ' . $visitor->id);
    
            return response()->json(['success' => true, 'visitor' => $visitor, 'checkInTime' => $checkInTime]);
        }
    
        Log::warning('Visitor not found for QR Code: ' . $request->qr_code);
        return response()->json(['success' => false, 'message' => 'Visitor not found.']);
    }    

    public function downloadQrCode($id)
    {
        $visitor = Visitor::findOrFail($id);
        $qrCode = QrCode::format('png')->size(250)->generate(route('visitor.undangan', $id));
        
        return response()->stream(
            function () use ($qrCode) {
                echo $qrCode;
            },
            200,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qr_code.png"',
            ]
        );
    }
    
    public function downloadPdf(Request $request)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
    
        $dompdf = new Dompdf($options);
    
        // Ambil parameter dari request
        $date = $request->input('date');
        $room = $request->input('room');
        $affiliation = $request->input('affiliation');
    
        // Buat query untuk mengambil data berdasarkan parameter yang diterima
        $query = CheckInTime::with('visitor');
    
        if ($date) {
            $query->whereDate('check_in_time', $date);
        }
    
        if ($room) {
            $query->where('room', $room);
        }
    
        if ($affiliation) {
            $query->whereHas('visitor', function ($q) use ($affiliation) {
                $q->where('affiliation', 'LIKE', '%' . $affiliation . '%');
            });
        }
    
        $checkInTimes = $query->get();
    
        // Load HTML content
        $html = view('visitor.pdf', compact('checkInTimes'))->render();
        $dompdf->loadHtml($html);
    
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        // Output the generated PDF to Browser
        return $dompdf->stream("data.pdf", array("Attachment" => 0));
    }
}
