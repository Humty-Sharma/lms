<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AcceptInvitationRequest;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService
    ) {}

    /**
     * Show form / list for invitations.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        Gate::authorize('create', Invitation::class);

        $invitationsQuery = Invitation::with(['company', 'inviter'])->latest();

        if ($user->isAdmin()) {
            $invitationsQuery->where('company_id', $user->company_id);
        }

        $invitations = $invitationsQuery->paginate(15);

        return view('invitations.index', compact('invitations', 'user'));
    }

    /**
     * Create an invitation.
     */
    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $user = $request->user();

        Gate::authorize('create', Invitation::class);

        if ($user->isSuperAdmin()) {
            $result = $this->invitationService->createBySuperAdmin(
                $user,
                $request->input('company_name'),
                $request->input('email')
            );
        } else {
            // Admin user
            $role = UserRole::from($request->input('role'));
            $result = $this->invitationService->createByAdmin(
                $user,
                $request->input('email'),
                $role
            );
        }

        $inviteUrl = route('invitations.accept.show', ['token' => $result['plainToken']]);

        return redirect()->route('invitations.index')
            ->with('success', 'Invitation created successfully!')
            ->with('invitation_url', $inviteUrl)
            ->with('invited_email', $result['invitation']->email);
    }

    /**
     * Show acceptance form for invitee.
     */
    public function showAccept(string $token): View
    {
        $invitation = $this->invitationService->findValidInvitation($token);

        if (! $invitation) {
            return view('invitations.invalid');
        }

        return view('invitations.accept', [
            'invitation' => $invitation,
            'token' => $token,
        ]);
    }

    /**
     * Process acceptance of the invitation.
     */
    public function processAccept(AcceptInvitationRequest $request, string $token): RedirectResponse
    {
        $invitation = $this->invitationService->findValidInvitation($token);

        if (! $invitation) {
            return redirect()->route('login')->withErrors([
                'email' => 'This invitation is invalid, expired, or has already been used.',
            ]);
        }

        $user = $this->invitationService->acceptInvitation(
            $invitation,
            $request->input('name'),
            $request->input('password')
        );

        // Auto-login newly registered user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account activated successfully.');
    }

    public function revoke(Request $request, Invitation $invitation): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin() && (int) $user->company_id !== (int) $invitation->company_id) {
            abort(403, 'Unauthorized company action.');
        }

        if (! $invitation->accepted_at && ! $invitation->revoked_at) {
            $invitation->update(['revoked_at' => now()]);
        }

        return back()->with('success', 'Invitation has been revoked.');
    }
}

