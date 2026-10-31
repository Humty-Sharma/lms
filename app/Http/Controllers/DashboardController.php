<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the role-appropriate dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $shortUrls = ShortUrl::with(['company', 'user'])
            ->visibleTo($user)
            ->latest()
            ->paginate(10);

        $companies = collect();
        $stats = [];

        if ($user->isSuperAdmin()) {
            // SuperAdmin
            $companies = Company::withCount(['users', 'shortUrls'])->latest()->get();
            $stats = [
                'total_companies' => Company::count(),
                'total_users' => User::count(),
                'total_urls' => ShortUrl::count(),
            ];
        } elseif ($user->isAdmin()) {
            // Admin
            $stats = [
                'company_users' => User::where('company_id', $user->company_id)->count(),
                'company_urls' => ShortUrl::where('company_id', $user->company_id)->count(),
                'my_urls' => ShortUrl::where('company_id', $user->company_id)->where('user_id', $user->id)->count(),
            ];
        } else {
            // Member
            $stats = [
                'my_urls' => ShortUrl::where('company_id', $user->company_id)->where('user_id', $user->id)->count(),
            ];
        }

        return view('dashboard', compact('user', 'shortUrls', 'companies', 'stats'));
    }
}

