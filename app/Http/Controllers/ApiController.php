<?php

namespace App\Http\Controllers;

use App\Services\ApiAuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $apiAuthService;

    public function __construct(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function test(Request $request)
    {
        return 'test';
    }

    public function getTariffs(Request $request)
    {
        $token = $this->apiAuthService->getToken();

        $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/tarificateur', $request->all());

        return $response->json();
    }

    public function savePolicy(Request $request)
    {
        $token = $this->apiAuthService->getToken();

        $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/saveContrat', $request->all());

        return $response->json();
    }
}