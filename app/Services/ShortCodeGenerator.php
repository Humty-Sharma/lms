<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use RuntimeException;

class ShortCodeGenerator
{
    public function generateUnique(int $length = 6, int $maxAttempts = 5): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = Str::lower(Str::random($length));

            if (! ShortUrl::where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate a unique short code. Please try again.');
    }
}
