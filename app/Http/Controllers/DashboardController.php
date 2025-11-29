<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EventCertificate;
use App\Models\CertificateDownload;
use App\Models\MembershipTypes;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('dashboard.home');
    }

    public function certificates()
    {
        $member = Auth::user();
        
        // Get event certificates - use the correct relationship
        $eventCertificates = $member->eventCertificates()
                                  ->where('status', 'completed')
                                  ->latest()
                                  ->get();
        
        // Get download history
        $downloadHistory = $member->certificateDownloads()
                                ->latest()
                                ->take(10)
                                ->get();
        
        return view('dashboard.certificate', compact('eventCertificates', 'downloadHistory'));
    }
    
    public function requestCertificate(){
        $user = Auth::user();
        $member_type = $user->membership_type;
        //dd($member_type);
        $membershipType = MembershipTypes::where('membership_type', $member_type)->first()->toArray();
        return view('dashboard.request-certificate')->with('member_type', $membershipType);
    }
    public function downloadCertificate(Request $request)
    {
        $member = Auth::user();
        $certificateType = $request->input('certificate_type');
        
        // Log the download
        $this->logCertificateDownload($member, $certificateType, $request->ip());
        
        if ($certificateType === 'membership') {
            return $this->generateMembershipCertificate($member);
        } elseif ($certificateType === 'event') {
            $eventId = $request->input('event_id');
            return $this->generateEventCertificate($member, $eventId);
        }
        
        return back()->with('error', 'Invalid certificate type.');
    }
    
    private function generateMembershipCertificate($member)
    {
        $data = [
            'member' => $member,
            'certificateId' => 'ACATA-MEM-' . $member->member_id,
            'issueDate' => now()->format('F j, Y'),
            'memberSince' => $member->created_on->format('F Y'),
        ];
        
        // Check if member is active
        if (!$member->isActive()) {
            return back()->with('error', 'Your membership is not active. Please renew your membership to download certificates.');
        }
        
        $pdf = Pdf::loadView('certificates.membership', $data)
                  ->setPaper('a4', 'landscape')
                  ->setOptions([
                      'dpi' => 150,
                      'defaultFont' => 'sans-serif',
                  ]);
        
        $filename = "ACATA-Membership-Certificate-{$member->member_id}.pdf";
        
        return $pdf->download($filename);
    }
    
    private function generateEventCertificate($member, $eventId)
    {
        // Find the event certificate
        $eventCertificate = EventCertificate::where('id', $eventId)
                                          ->where('member_id', $member->id)
                                          ->where('status', 'completed')
                                          ->first();
        
        if (!$eventCertificate) {
            return back()->with('error', 'Event certificate not found or not available for download.');
        }
        
        $data = [
            'member' => $member,
            'eventCertificate' => $eventCertificate,
            'certificateId' => $eventCertificate->certificate_id,
            'issueDate' => $eventCertificate->created_at->format('F j, Y'),
        ];
        
        $pdf = Pdf::loadView('certificates.event', $data)
                  ->setPaper('a4', 'landscape')
                  ->setOptions([
                      'dpi' => 150,
                      'defaultFont' => 'sans-serif',
                  ]);
        
        $filename = "ACATA-Event-Certificate-{$eventCertificate->event_name}-{$member->member_id}.pdf";
        $filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $filename); // Sanitize filename
        
        return $pdf->download($filename);
    }
    
    private function logCertificateDownload($member, $certificateType, $ipAddress)
    {
        $certificateId = 'ACATA-MEM-' . $member->member_id;
        $eventName = null;
        
        if ($certificateType === 'event' && request()->has('event_id')) {
            $eventCertificate = EventCertificate::find(request()->input('event_id'));
            if ($eventCertificate) {
                $certificateId = $eventCertificate->certificate_id;
                $eventName = $eventCertificate->event_name;
            }
        }
        
        CertificateDownload::create([
            'member_id' => $member->id,
            'certificate_type' => $certificateType,
            'certificate_id' => $certificateId,
            'event_name' => $eventName,
            'ip_address' => $ipAddress,
            'downloaded_at' => now(),
        ]);
    }
    
    /**
     * Seed some sample event certificates for testing
     */
    public function seedSampleCertificates()
    {
        $member = Auth::user();
        
        // Create sample event certificates if none exist
        if ($member->eventCertificates()->count() === 0) {
            EventCertificate::create([
                'member_id' => $member->id,
                'event_name' => 'Annual CAT Conference 2024',
                'event_type' => 'conference',
                'event_date' => now()->subDays(30),
                'certificate_id' => EventCertificate::generateCertificateId($member->id, 'Annual CAT Conference 2024'),
                'status' => 'completed',
                'description' => 'Participated in the annual Computer Adaptive Testing conference',
            ]);
            
            EventCertificate::create([
                'member_id' => $member->id,
                'event_name' => 'Advanced Psychometrics Workshop',
                'event_type' => 'workshop',
                'event_date' => now()->subDays(15),
                'certificate_id' => EventCertificate::generateCertificateId($member->id, 'Advanced Psychometrics Workshop'),
                'status' => 'completed',
                'description' => 'Completed advanced psychometrics training workshop',
            ]);
        }
        
        return back()->with('success', 'Sample certificates added for testing.');
    }

}
