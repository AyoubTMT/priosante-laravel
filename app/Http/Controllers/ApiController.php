<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ApiAuthService;
use App\Services\TarificationService;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

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
    /**
     * Handle the request to get tariffs based on user input.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTariffs(Request $request)
    {
        Log::info('Request data:', $request->all());

        $token = $this->apiAuthService->getToken();
        if (!$token) {
            Log::error('Failed to retrieve API token');
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        $formattedRequestData = $this->tarificationService->formatRequestData($request->all());
        Log::info('Formatted request data:', $formattedRequestData);

        try {
            $response = Http::withToken($token)
                ->timeout(60) // Increase the timeout to 60 seconds
                ->post("https://ws.eca-partenaires.com/api/tarificateur", $formattedRequestData);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('API response data:', $responseData);

                // Calculate age from dateNaissance
                $dateNaissance = $request->input('dateNaissance');
                $age = Carbon::parse($dateNaissance)->age;

                // Get budget from request
                $budget = $request->input('budget');

                // Get filtered and ordered tariffs
                $result = $this->tarificationService->getFilteredAndOrderedTariffs($responseData, $age, $budget);

                Log::info('Filtered and ordered tariffs:', $result);

                return response()->json($result);
            } else {
                Log::error('API request failed with status code: ' . $response->status(), [
                    'response' => $response->body()
                ]);
                return response()->json(['error' => 'API request failed'], $response->status());
            }
        } catch (RequestException $e) {
            Log::error('Request failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }


    public function saveDevis(Request $request)
    {
        $token = $this->apiAuthService->getToken();
        $formattedData = $this->tarificationService->formatRequestData($request->all());

        $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/saveContrat', $formattedData);

        return $response->json();
    }
}
