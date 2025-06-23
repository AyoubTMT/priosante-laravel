<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\Services\ApiAuthService;
use App\Services\TarificationService;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    protected $apiAuthService;
    protected $tarificationService;

    public function __construct(ApiAuthService $apiAuthService, TarificationService $tarificationService)
    {
        $this->apiAuthService = $apiAuthService;
        $this->tarificationService = $tarificationService;
    }

    public function test(Request $request)
    {
        return 'test';
    }

    public function getTariffs(Request $request)
    {
        Log::info($request->all());

        $token = $this->apiAuthService->getToken();
        $formattedData = $this->tarificationService->formatRequestData($request->all());
        Log::info('Formatted Data: ', $formattedData);
        
        $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/tarificateur', $formattedData);

        Log::info('Formatted response: ', $response->json());
        return $response->json();
    }

    public function saveDevis(Request $request)
    {
        $token = $this->apiAuthService->getToken();
        $formattedData = $this->tarificationService->formatRequestData($request->all());

        $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/saveContrat', $formattedData);

        return $response->json();
    }
}
