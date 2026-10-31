<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies (SuperAdmin only).
     */
    public function index(): View
    {
        $companies = Company::withCount(['users', 'shortUrls', 'invitations'])
            ->latest()
            ->paginate(15);

        return view('companies.index', compact('companies'));
    }

    /**
     * Display company details, members, URLs, and invitations (SuperAdmin only).
     */
    public function show(Company $company): View
    {
        $company->loadCount(['users', 'shortUrls', 'invitations']);

        $users = $company->users()->latest()->paginate(10, ['*'], 'users_page');
        $shortUrls = $company->shortUrls()->with('user')->latest()->paginate(10, ['*'], 'urls_page');
        $invitations = $company->invitations()->with('inviter')->latest()->paginate(10, ['*'], 'invites_page');

        return view('companies.show', compact('company', 'users', 'shortUrls', 'invitations'));
    }
}
