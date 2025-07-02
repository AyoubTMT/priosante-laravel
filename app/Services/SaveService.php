<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\RequestException;

class SaveService
{
    public function formatRequestData(array $data)
    {
        // Paramètres statiques
        $formattedData = [
            "flag" => "DEVIS_COMPLET",
            "courtier" => "testcourtier",
            "identifiantWs" => "testApi",
            "assure" => [],
            "produits" => [
                "types" => "SANTE",
                "SANTE" => [
                    "produit" => $data['selectedTarif']['produit'] ?? '',
                    "regime" => $data['baseInfo']['regime'] ?? '',
                    "soinsGeneraux" => "MOYEN",
                    "optique" => "MOYEN",
                    "hospitalisation" => "MOYEN",
                    "dentaire" => "MOYEN",
                    "nombrevisiteAnnuellegGeneraliste" => "DE_3_A_6_FOIS",
                    "priseEnChargePrestationConfortHospitalisation" => "OUI",
                    "depassementsHonoraires" => "OUI",
                    "portLunettesLentilles" => "OUI",
                    "soinsDentaire" => "OUI",
                    "franchise" => "NON",
                    "pharmaPlus" => "NON",
                    "renfortPlus" => "NON",
                    "repriseConcurrence" => "OUI",
                    "dateEffet" => $this->formatDate($data['baseInfo']['dateEffet'] ?? ''),
                    "periodicite" => "MENSUELLE",
                    "formuleRecommande" => $data['selectedTarif']['formule'] ?? '',
                    "formuleChoisi" => $data['selectedTarif']['formule'] ?? '',
                    "fraisDossier" => "20",
                    "modePaiement" => $data['modePaiement'] ?? '',
                    "modePaiementCotisationSuivante" => $data['modePaiement'] ?? '',
                    "assureur" => [
                        "nomAssureur" => $data['souscripteurInfo']['nom'] . ' ' . $data['souscripteurInfo']['prenom'],
                        "referenceContrat" => "aaaaaaa",
                        "dateEcheanceContrat" => $this->formatDate($data['baseInfo']['dateEffet'] ?? ''),
                        "numero" => null,
                        "typeVoie" => $data['souscripteurInfo']['typeVoie'] ?? '',
                        "libelle" => $data['souscripteurInfo']['voie'] ?? '',
                        "batiment" => null,
                        "complement" => "Complément",
                        "codePostal" => $data['baseInfo']['codePostal'] ?? '',
                        "ville" => $data['souscripteurInfo']['ville'] ?? ''
                    ]
                ]
            ],
            "souscripteur" => [
                "profession" => $data['souscripteurInfo']['profession'] ?? '',
                "voie" => $data['souscripteurInfo']['voie'] ?? '',
                "ville" => $data['souscripteurInfo']['ville'] ?? '',
                "codePostal" => $data['baseInfo']['codePostal'] ?? '',
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
                "assurerConjoint" => isset($data['baseInfo']['assure']) && strpos($data['baseInfo']['assure'], 'couple') !== false ? "OUI" : "NON",
                "payeurDifferent" => $data['payeurInfo']['payeurDifferent'] ?? ''
            ],
            "payeur" => [
                "ibanPrelevemnt" => $data['payeurInfo']['ibanPrelevemnt'] ?? '',
                "ibanRembDifferent" => $data['payeurInfo']['ibanRembDifferent'] ?? '',
                "ibanRemboursement" => $data['payeurInfo']['ibanRemboursement'] ?? '',
                "mandatSepa" => $data['payeurInfo']['mandatSepa'] ?? '',
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

        // Remplir l'objet assure en fonction de souscripteurIsAssure
        if (isset($data['souscripteurInfo']['souscripteurIsAssure']) && $data['souscripteurInfo']['souscripteurIsAssure'] === 'OUI') {
            $formattedData['assure'] = [
                "cv" => $data['souscripteurInfo']['cv'] ?? '',
                "nom" => $data['souscripteurInfo']['nom'] ?? '',
                "prenom" => $data['souscripteurInfo']['prenom'] ?? '',
                "dateNaissance" => $this->formatDate($data['souscripteurInfo']['dateNaissance'] ?? ''),
                "ayantDroitDe" => $data['assureInfo']['ayantDroitDe'] ?? '',
                "numeroSS" => $data['assureInfo']['numeroSS'] ?? ''
            ];
        } else {
            $formattedData['assure'] = [
                "cv" => $data['assureInfo']['cv'] ?? '',
                "nom" => $data['assureInfo']['nom'] ?? '',
                "prenom" => $data['assureInfo']['prenom'] ?? '',
                "dateNaissance" => $this->formatDate($data['assureInfo']['dateNaissance'] ?? ''),
                "ayantDroitDe" => $data['assureInfo']['ayantDroitDe'] ?? '',
                "numeroSS" => $data['assureInfo']['numeroSS'] ?? ''
            ];
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
                return $response->json();
            } else {
                Log::error('Save request failed with status code: ' . $response->status(), [
                    'response' => $response->body()
                ]);
                return ['error' => 'Save request failed'];
            }
        } catch (RequestException $e) {
            Log::error('Save request failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return ['error' => 'Save request failed: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return ['error' => 'An unexpected error occurred'];
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
