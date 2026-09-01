<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleTokenVerifier
{
    /**
     * Verifies a Google OAuth access token by asking Google's own userinfo
     * endpoint who it belongs to. Returns the verified payload (email, name,
     * picture, email_verified) or null if the token is invalid/expired.
     */
    public function verify(string $accessToken): ?array
    {
        $response = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        if (empty($data['email'])) {
            return null;
        }

        return $data;
    }
}
