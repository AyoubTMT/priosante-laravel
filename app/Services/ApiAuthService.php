<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiAuthService
{
    protected $baseUrl = 'https://ws.eca-partenaires.com/api';
    protected $identifiant = 'testApi';
    protected $motDePasse = '2qw&8!07';

    public function getToken()
    {
        // Check if token is already cached
        if (Cache::has('api_token')) {
            return Cache::get('api_token');
        }

        // If not, call the login API
        $response = Http::post($this->baseUrl . '/login', [
            'identifiant' => $this->identifiant,
            'motDePasse' => $this->motDePasse,
        ]);

        if ($response->successful()) {
            $token = $response->json()['token'];
            // Cache the token for 24 hours
            Cache::put('api_token', $token, now()->addHours(24));
            return $token;
        }

        throw new \Exception('Failed to retrieve token');
    }
}
