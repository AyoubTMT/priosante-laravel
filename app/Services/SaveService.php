<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class SaveService
{
    public function formatRequestData(array $data)
    {
        // Paramètres statiques
        $formattedData = [
            "flag" => "DEVIS_COMPLET",
            "flagType" => $data['flagType'] ?? "LIEN",
            "courtier" => "testcourtier",
            "identifiantWs" => "testApi",
            "bubblein" => "OUI",
            "assure" => [
                "cv" => $data['souscripteurInfo']['cv'] ?? '',
                "nom" => $data['souscripteurInfo']['nom'] ?? '',
                "prenom" => $data['souscripteurInfo']['prenom'] ?? '',
                "dateNaissance" => $this->formatDate($data['souscripteurInfo']['dateNaissance'] ?? ''),
                "codeOrga" => $data['souscripteurInfo']['codeOrga'] ?? '',
                "numeroSS" => $data['souscripteurInfo']['numeroSS'] ?? '',
                "ayantDroitDe" => $data['souscripteurInfo']['ayantDroitDe'] ?? 'AUCUN'
            ],
            "produits" => [
                "types" => "SANTE",
                "SANTE" => [
                    "produit" => $data['selectedTarif']['produit'] ?? '',
                    "pharmaPlus" => "NON",
                    "renfortPlus" => "NON",
                    "regime" => $data['baseInfo']['regime'] ?? '',
                    "soinsGeneraux" => "FORT",
                    "optique" => "MOYEN",
                    "hospitalisation" => "FORT",
                    "dentaire" => "MOYEN",
                    "nombrevisiteAnnuellegGeneraliste" => "DE_3_A_6_FOIS",
                    "priseEnChargePrestationConfortHospitalisation" => "OUI",
                    "depassementsHonoraires" => "OUI",
                    "portLunettesLentilles" => "OUI",
                    "soinsDentaire" => "OUI",
                    "appareilAuditif" => "MOYEN",
                    "franchise" => "NON",
                    "repriseConcurrence" => "NON",
                    "dateEffet" => $this->formatDate($data['baseInfo']['dateEffet'] ?? ''),
                    "periodicite" => "MENSUELLE",
                    "formuleRecommande" => $data['selectedTarif']['formule'] ?? '',
                    "formuleChoisi" => $data['selectedTarif']['formule'] ?? '',
                    "fraisDossier" => "0",
                    "modePaiement" => $data['modePaiement'] ?? '',
                    "modePaiementCotisationSuivante" => $data['modePaiement'] ?? '',
                    "enfants" => []
                ]
            ],
            "souscripteur" => [
                "profession" => $data['souscripteurInfo']['profession'] ?? '',
                "voie" => $data['souscripteurInfo']['voie'] ?? '',
                "codePostal" => $data['baseInfo']['codePostal'] ?? '',
                "ville" => $data['souscripteurInfo']['ville'] ?? '',
                "nom" => $data['souscripteurInfo']['nom'] ?? '',
                "cv" => $data['souscripteurInfo']['cv'] ?? '',
                "revenuMensuel" => $data['souscripteurInfo']['revenuMensuel'] ?? '',
                "typeSouscripteur" => $data['souscripteurInfo']['typeSouscripteur'] ?? '',
                "tel" => $data['souscripteurInfo']['tel'] ?? '',
                "dateNaissance" => $this->formatDate($data['souscripteurInfo']['dateNaissance'] ?? ''),
                "prenom" => $data['souscripteurInfo']['prenom'] ?? '',
                "situationFam" => $data['souscripteurInfo']['situationFam'] ?? '',
                "email" => $data['souscripteurInfo']['email'] ?? '',
                "souscripteurIsAssure" => $data['souscripteurInfo']['souscripteurIsAssure'] ?? '',
                "ayantDroitDe" => $data['souscripteurInfo']['ayantDroitDe'] ?? 'AUCUN',
                "numeroSS" => $data['souscripteurInfo']['numeroSS'] ?? '',
                "assurerConjoint" => isset($data['baseInfo']['assure']) && strpos($data['baseInfo']['assure'], 'couple') !== false ? "OUI" : "NON",
                "payeurDifferent" => $data['payeurInfo']['payeurDifferent'] ?? ''
            ],
            "conjoint" => [
                "cv" => $data['conjointInfo']['cv'] ?? '',
                "nom" => $data['conjointInfo']['nom'] ?? '',
                "prenom" => $data['conjointInfo']['prenom'] ?? '',
                "dateNaissance" => $this->formatDate($data['conjointInfo']['dateNaissance'] ?? ''),
                "codeOrga" => $data['conjointInfo']['codeOrga'] ?? '',
                "numeroSS" => $data['conjointInfo']['numeroSS'] ?? '',
                "ayantDroitDe" => $data['conjointInfo']['ayantDroitDe'] ?? 'AUCUN'
            ],
            "payeur" => [
                "ibanPrelevemnt" => $data['payeurInfo']['ibanPrelevemnt'] ?? '',
                "ibanRembDifferent" => $data['payeurInfo']['ibanRembDifferent'] ?? '',
                "ibanRemboursement" => $data['payeurInfo']['ibanRemboursement'] ?? '',
                "mandatSepa" => $data['payeurInfo']['mandatSepa'] ?? '',
                "rum" => $data['payeurInfo']['rum'] ?? '',
                "payeurDifferent" => $data['payeurInfo']['payeurDifferent'] ?? '',
                "nomPayeur" => $data['payeurInfo']['nomPayeur'] ?? '',
                "prenomPayeur" => $data['payeurInfo']['prenomPayeur'] ?? '',
                "numeroPayeur" => $data['payeurInfo']['numeroPayeur'] ?? '',
                "typeVoiePayeur" => $data['payeurInfo']['typeVoiePayeur'] ?? '',
                "voiePayeur" => $data['payeurInfo']['voiePayeur'] ?? '',
                "batimentPayeur" => $data['payeurInfo']['batimentPayeur'] ?? '',
                "libellePayeur" => $data['payeurInfo']['libellePayeur'] ?? '',
                "codePostalPayeur" => $data['payeurInfo']['codePostalPayeur'] ?? '',
                "villePayeur" => $data['payeurInfo']['villePayeur'] ?? ''
            ]
        ];

        // Remplir les enfants
        if (isset($data['enfantsInfo']) && is_array($data['enfantsInfo'])) {
            foreach ($data['enfantsInfo'] as $index => $enfant) {
                $formattedData['produits']['SANTE']['enfants'][] = [
                    "cv" => $enfant['cv'] ?? '',
                    "nom" => $enfant['nom'] ?? '',
                    "prenom" => $enfant['prenom'] ?? '',
                    "dateNaissance" => $this->formatDate($enfant['dateNaissance'] ?? ''),
                    "poursuiteEtude" => $enfant['poursuiteEtude'] ?? 'NON',
                    "ayantDroitDe" => $enfant['ayantDroitDe'] ?? '',
                    "numeroSS" => $enfant['numeroSS'] ?? null,
                    "codeOrga" => $enfant['codeOrga'] ?? null,
                    "ayantDroit" => $enfant['ayantDroit'] ?? []
                ];

                if (isset($enfant['ayantDroitDe']) && $enfant['ayantDroitDe'] === 'AUCUN') {
                    $formattedData['produits']['SANTE']['enfants'][$index]['numeroSS'] = $enfant['numeroSS'] ?? '';
                    $formattedData['produits']['SANTE']['enfants'][$index]['codeOrga'] = $enfant['codeOrga'] ?? '';
                }

                if (isset($enfant['ayantDroitDe']) && $enfant['ayantDroitDe'] === 'AUTRE') {
                    $formattedData['produits']['SANTE']['enfants'][$index]['ayantDroit'] = [
                        "cv" => $enfant['cvAyantDroit'] ?? '',
                        "nom" => $enfant['nomAyantDroit'] ?? '',
                        "prenom" => $enfant['prenomAyantDroit'] ?? '',
                        "dateNaissance" => $this->formatDate($enfant['dateNaissanceAyantDroit'] ?? ''),
                        "numeroSS" => $enfant['numeroSSAyantDroit'] ?? '',
                        "codeOrga" => $enfant['codeOrgaAyantDroit'] ?? ''
                    ];
                }
            }
        }

        return $formattedData;
    }

    public function save($token, array $data)
    {
        Log::info('Save request data:', $data);
        $formattedData = $this->formatRequestData($data);
        Log::info('Formatted save request data:', $formattedData);

        try {
            $response = Http::withToken($token)->post('https://ws.eca-partenaires.com/api/saveContrat', $formattedData);

            if ($response->successful()) {
                Log::info('Save response data:', $response->json());
                return response()->json([
                    'message' => 'JSON sent successfully!',
                    'response' => $response->json()
                ], 200);
            } else {
                Log::error('Save request failed with status code: ' . $response->status(), [
                    'response' => $response->body()
                ]);
                return response()->json([
                    'message' => 'Failed to send JSON.',
                    'error' => $response->body()
                ], $response->status());
            }
        } catch (RequestException $e) {
            Log::error('Save request failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Save request failed.', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'An unexpected error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    protected function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        $dateObj = new \DateTime($date);
        return $dateObj->format('d/m/Y');
    }
}
