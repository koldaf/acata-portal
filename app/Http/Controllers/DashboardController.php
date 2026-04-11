<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Event;
use App\Models\EventCertificate;
use App\Models\EventRegistration;
use App\Models\Interest;
use App\Models\CertificateSetting;
use App\Models\CertificateDownload;
use App\Models\LibraryResource;
use App\Models\MembershipTypes;
use App\Models\Members;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $members = new Members();

        $recentActivities = $members->recentActivities()->get();
        return view('dashboard.home')->with(['recentActivities' => $recentActivities]);
    }

    //profile management
    public function profile()
    {
        $user = Auth::user();
        $member_type = $user->membership_type;
        $membershipType = MembershipTypes::where('membership_type', $member_type)->first()->toArray();
        return view('dashboard.profile')->with(['member_type' => $membershipType, 'user' => $user]);
    }

    public function updateProfileBio(Request $request)
    {
        $user = Auth::user();
        // Validate input
        $request->validate([
            'bio' => 'nullable|string|max:1000',
        ]);
        
        // Update bio
        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }
        
       /* // Handle profile picture upload
        if ($request->hasFile('profilePicture')) {
            $file = $request->file('profilePicture');
            $filename = 'profile_' . $user->id . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/profile_pictures', $filename);
            $user->profile_picture = 'storage/profile_pictures/' . $filename;
        }
        */
        $user->save();
        
        return back()->with('success', 'Profile updated successfully.');
    }
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();
        
        // Validate the uploaded file
        $request->validate([
            'profilePicture' => 'required|image|mimes:jpeg,png|max:2048', // Max 2MB
        ]);
        
        if ($request->hasFile('profilePicture')) {
            $file = $request->file('profilePicture');
            $filename = 'profile_' . $user->id . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/profile_pictures', $filename);
            $user->profile_picture = $filename;
            $user->save();
            
            return back()->with('success', 'Profile picture updated successfully.');
        }
        
        return back()->with('error', 'No profile picture uploaded.');
    }

    public function viewProfilePicture(?string $filename = null)
    {
        $member = Auth::user();
        $storedValue = $filename ?: $member?->profile_picture;

        if (empty($storedValue)) {
            abort(404);
        }

        if (filter_var($storedValue, FILTER_VALIDATE_URL)) {
            return redirect()->away($storedValue);
        }

        $safeFilename = basename($storedValue);
        $relativePath = 'profile_pictures/' . $safeFilename;

        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($relativePath);

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function interests()
    {
        $member = Auth::user();

        $memberInterests = $member->interests()
            ->orderBy('interest')
            ->get();

        $availableInterests = Interest::query()
            ->active()
            ->whereNotIn('id', $memberInterests->pluck('id'))
            ->orderBy('interest')
            ->get();

        return view('dashboard.interests', [
            'member' => $member,
            'memberInterests' => $memberInterests,
            'availableInterests' => $availableInterests,
        ]);
    }

    public function addInterest(Request $request)
    {
        $member = Auth::user();

        $validated = $request->validate([
            'interest_id' => 'nullable|integer|exists:interests,id|required_without:interest_name',
            'interest_name' => 'nullable|string|max:120|required_without:interest_id',
        ]);

        if (!empty($validated['interest_id'])) {
            $interest = Interest::query()
                ->active()
                ->find($validated['interest_id']);

            if (!$interest) {
                return back()->with('error', 'Selected interest is unavailable.');
            }
        } else {
            $interestName = trim((string) ($validated['interest_name'] ?? ''));

            if ($interestName === '') {
                return back()->with('error', 'Please provide an interest name.');
            }

            $interest = Interest::firstOrCreate(
                ['interest' => $interestName],
                ['status' => 'active']
            );

            if ($interest->status !== 'active') {
                $interest->update(['status' => 'active']);
            }
        }

        if ($member->hasInterest($interest->id)) {
            return back()->with('error', 'You already have this interest in your profile.');
        }

        $member->addInterests([$interest->id]);

        return back()->with('success', 'Interest added successfully.');
    }

    public function removeInterest(Interest $interest)
    {
        $member = Auth::user();

        if (!$member->hasInterest($interest->id)) {
            return back()->with('error', 'This interest is not on your profile.');
        }

        $member->removeInterests([$interest->id]);

        return back()->with('success', 'Interest removed successfully.');
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
        return view('dashboard.request-certificate')->with(['member_type' => $membershipType, 'user' => $user]);
    }

    public function resources()
    {
        $user = Auth::user();

        $resources = LibraryResource::query()
            ->when(!$user->isAdmin(), function ($query) {
                $query->where('visibility', 'members');
            })
            ->latest()
            ->paginate(15);

        return view('dashboard.resources', [
            'resources' => $resources,
            'user' => $user,
        ]);
    }

    public function downloadResource(LibraryResource $resource)
    {
        $user = Auth::user();

        if ($resource->visibility === 'admins' && !$user->isAdmin()) {
            abort(403, 'You do not have permission to access this resource.');
        }

        if (!Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'Resource file was not found.');
        }

        $fullPath = Storage::disk('public')->path($resource->file_path);

        return response()->download($fullPath, $resource->file_name);
    }

    public function events()
    {
        $events = Event::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('starts_at', '>=', now())
                    ->orWhere(function ($liveQuery) {
                        $liveQuery->where('starts_at', '<=', now())
                            ->where(function ($endQuery) {
                                $endQuery->whereNull('ends_at')
                                    ->orWhere('ends_at', '>=', now());
                            });
                    });
            })
            ->withCount('registrations')
            ->orderBy('starts_at')
            ->paginate(12);

        return view('dashboard.events.index', [
            'events' => $events,
        ]);
    }

    public function showEvent(Event $event)
    {
        abort_unless($event->status === 'published', 404);

        $member = Auth::user();
        $isRegistered = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->exists();

        $registrationsCount = EventRegistration::query()
            ->where('event_id', $event->id)
            ->count();

        $isFull = $event->capacity !== null && $registrationsCount >= $event->capacity;
        $isEnded = $event->ends_at !== null && $event->ends_at->isPast();
        $hasStarted = $event->starts_at !== null && $event->starts_at->isPast();
        $registrationAllowed = $event->registration_open && !$isEnded && !$isRegistered && !$isFull;

        return view('dashboard.events.show', [
            'event' => $event,
            'isRegistered' => $isRegistered,
            'registrationsCount' => $registrationsCount,
            'isFull' => $isFull,
            'isEnded' => $isEnded,
            'hasStarted' => $hasStarted,
            'registrationAllowed' => $registrationAllowed,
        ]);
    }

    public function registerForEvent(Request $request, Event $event)
    {
        abort_unless($event->status === 'published', 404);

        $member = Auth::user();

        if (!$event->registration_open) {
            return back()->with('error', 'Registration is currently closed for this event.');
        }

        if ($event->ends_at !== null && $event->ends_at->isPast()) {
            return back()->with('error', 'This event has ended, so registration is no longer available.');
        }

        $alreadyRegistered = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        $registrationsCount = EventRegistration::query()
            ->where('event_id', $event->id)
            ->count();

        if ($event->capacity !== null && $registrationsCount >= $event->capacity) {
            return back()->with('error', 'This event is full and no longer accepting registrations.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'registered',
            'notes' => $validated['notes'] ?? null,
            'registered_at' => now(),
        ]);

        return redirect()
            ->route('dashboard.events.show', $event)
            ->with('success', 'You have been registered for this event successfully.');
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
        $setting = CertificateSetting::where('certificate_type', 'membership')->first();

        $data = [
            'member' => $member,
            'certificateId' => 'ACATA-MEM-' . $member->member_id,
            'issueDate' => now()->format('F j, Y'),
            'memberSince' => $member->created_on->format('F Y'),
            'setting' => $setting,
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
        
        $setting = CertificateSetting::where('certificate_type', 'event')->first();

        $data = [
            'member' => $member,
            'eventCertificate' => $eventCertificate,
            'certificateId' => $eventCertificate->certificate_id,
            'issueDate' => $eventCertificate->created_at->format('F j, Y'),
            'setting' => $setting,
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
