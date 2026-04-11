<?php

namespace App\Http\Controllers;

use App\Models\CertificateSetting;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\LibraryResource;
use App\Models\Members;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'memberCount' => Members::count(),
            'adminCount' => Members::admins()->count(),
            'superAdminCount' => Members::role(Members::ROLE_SUPER_ADMIN)->count(),
            'activeMemberCount' => Members::active()->count(),
        ]);
    }

    public function members(Request $request): View
    {
        $query = Members::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->search($request->string('search')->toString());
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return view('admin.members.index', [
            'members' => $query->paginate(20)->withQueryString(),
            'roles' => [
                Members::ROLE_MEMBER,
                Members::ROLE_ADMIN,
                Members::ROLE_SUPER_ADMIN,
            ],
            'currentUser' => Auth::user(),
        ]);
    }

    public function updateRole(Request $request, Members $member): RedirectResponse
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->isSuperAdmin()) {
            abort(403, 'Only super admins can assign roles.');
        }

        $validated = $request->validate([
            'role' => 'required|in:member,admin,super_admin',
        ]);

        $newRole = $validated['role'];

        if ($member->id === $currentUser->id && $newRole !== Members::ROLE_SUPER_ADMIN) {
            return back()->with('error', 'You cannot remove your own super admin role.');
        }

        if ($member->isSuperAdmin() && $newRole !== Members::ROLE_SUPER_ADMIN) {
            $otherSuperAdmins = Members::role(Members::ROLE_SUPER_ADMIN)
                ->where('id', '!=', $member->id)
                ->count();

            if ($otherSuperAdmins === 0) {
                return back()->with('error', 'At least one super admin must remain in the system.');
            }
        }

        $member->update([
            'role' => $newRole,
            'role_assigned_by' => $currentUser->id,
            'role_assigned_at' => now(),
        ]);

        return back()->with('success', 'Role updated successfully for ' . $member->display_name . '.');
    }

    public function events(Request $request): View
    {
        $events = Event::query()
            ->withCount('registrations')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->orderBy('starts_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    public function createEvent(): View
    {
        return view('admin.events.create');
    }

    public function storeEvent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'capacity' => 'nullable|integer|min:1',
            'registration_open' => 'nullable|boolean',
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
        ]);

        $validated['registration_open'] = (bool) ($validated['registration_open'] ?? false);
        $validated['created_by'] = Auth::id();

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function editEvent(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    public function updateEvent(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'capacity' => 'nullable|integer|min:1',
            'registration_open' => 'nullable|boolean',
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
        ]);

        $validated['registration_open'] = (bool) ($validated['registration_open'] ?? false);
        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroyEvent(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function eventRegistrants(Event $event): View
    {
        $registrants = EventRegistration::query()
            ->where('event_id', $event->id)
            ->with('member')
            ->orderBy('registered_at', 'desc')
            ->paginate(20);

        $counts = [
            'total' => EventRegistration::where('event_id', $event->id)->count(),
            'registered' => EventRegistration::where('event_id', $event->id)->where('status', 'registered')->count(),
            'attended' => EventRegistration::where('event_id', $event->id)->where('status', 'attended')->count(),
            'cancelled' => EventRegistration::where('event_id', $event->id)->where('status', 'cancelled')->count(),
            'no_show' => EventRegistration::where('event_id', $event->id)->where('status', 'no_show')->count(),
        ];

        return view('admin.events.registrants', [
            'event' => $event,
            'registrants' => $registrants,
            'counts' => $counts,
        ]);
    }

    public function resources(): View
    {
        return view('admin.resources.index', [
            'resources' => LibraryResource::query()->with('uploader')->latest()->paginate(20),
        ]);
    }

    public function storeResource(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => ['required', Rule::in(['members', 'admins'])],
            'resource_file' => 'required|file|max:10240',
        ]);

        $file = $request->file('resource_file');
        $storedPath = $file->store('resources', 'public');

        LibraryResource::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Resource uploaded successfully.');
    }

    public function destroyResource(LibraryResource $resource): RedirectResponse
    {
        if (Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Resource deleted successfully.');
    }

    public function certificateSettings(): View
    {
        $settings = CertificateSetting::query()
            ->get()
            ->keyBy('certificate_type');

        return view('admin.certificates.settings', [
            'settings' => $settings,
            'types' => ['membership', 'event'],
        ]);
    }

    public function updateCertificateSetting(Request $request, string $type): RedirectResponse
    {
        abort_unless(in_array($type, ['membership', 'event'], true), 404);

        $validated = $request->validate([
            'signatory_name' => 'required|string|max:255',
            'signatory_title' => 'nullable|string|max:255',
            'signature' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        $setting = CertificateSetting::firstOrNew(['certificate_type' => $type]);

        if ($request->hasFile('signature')) {
            if ($setting->signature_path && Storage::disk('public')->exists($setting->signature_path)) {
                Storage::disk('public')->delete($setting->signature_path);
            }

            $extension = $request->file('signature')->getClientOriginalExtension();
            $setting->signature_path = $request->file('signature')->storeAs(
                'certificate-signatures',
                $type . '-signature.' . $extension,
                'public'
            );
        }

        $setting->fill([
            'signatory_name' => $validated['signatory_name'],
            'signatory_title' => $validated['signatory_title'] ?? null,
            'updated_by' => Auth::id(),
        ]);
        $setting->certificate_type = $type;
        $setting->save();

        return back()->with('success', ucfirst($type) . ' certificate settings updated successfully.');
    }

    public function viewCertificateSignature(CertificateSetting $setting)
    {
        if (!$setting->signature_path || !Storage::disk('public')->exists($setting->signature_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($setting->signature_path));
    }
}
