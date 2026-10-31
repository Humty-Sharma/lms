<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use App\Services\ShortCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function __construct(
        protected ShortCodeGenerator $codeGenerator
    ) {}

    /**
     * Display a listing of the short URLs.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $shortUrls = ShortUrl::with(['company', 'user'])
            ->visibleTo($user)
            ->latest()
            ->paginate(15);

        return view('short_urls.index', compact('shortUrls', 'user'));
    }

    /**
     * Store a newly created short URL.
     */
    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $user = $request->user();

        Gate::authorize('create', ShortUrl::class);

        try {
            $code = $this->codeGenerator->generateUnique();

            ShortUrl::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'original_url' => $request->input('original_url'),
                'code' => $code,
            ]);

            return back()->with('success', "Short URL created: " . url("/s/{$code}"));
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['original_url' => 'Could not generate short URL. Please try again.']);
        }
    }

    
    public function redirect(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();
        return redirect()->away($shortUrl->original_url, 302);
    }
}

