<?php

namespace App\Services;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TarificationService
{
    public function formatRequestData(array $data)
    {
        // Paramètres statiques
        $formattedData = [
            "produitChoisi" => "SANTE",
            "produit" => "ALL",
            "courtier" => "testcourtier",
            "identifiantWs" => "testApi"
        ];

        // Formater les dates
        if (isset($data['dateNaissance'])) {
            $formattedData['dateNaissance'] = $this->formatDate($data['dateNaissance']);
        }

        if (isset($data['dateNaissanceConjoint'])) {
            $formattedData['dateNaissanceConjoint'] = $this->formatDate($data['dateNaissanceConjoint']);
        }

        if (isset($data['dateEffet'])) {
            $formattedData['dateEffet'] = $this->formatDate($data['dateEffet']);
        }

        // Formater le régime
        if (isset($data['regime'])) {
            $formattedData['regime'] = $data['regime'];
        }

        // Formater le code postal
        if (isset($data['codePostal'])) {
            $formattedData['codePostal'] = $data['codePostal'];
        }

        // Formater le nombre d'enfants
        $dateNaissanceEnfants = [];
        if (isset($data['nbrEnfant'])) {
            $formattedData['nbrEnfant'] = $data['nbrEnfant'];
            // Formater les dates de naissance des enfants
            if($formattedData['nbrEnfant'] > 0){
                for ($i = 1; $i <= $formattedData['nbrEnfant']; $i++) {
                    if (isset($data["dateNaissanceEnfant{$i}"])) {
                        $dateNaissanceEnfants[] = $this->formatDate($data["dateNaissanceEnfant{$i}"]);
                    }
                }
            }
        }

        $formattedData['dateNaissanceEnfants'] = $dateNaissanceEnfants;

        // Formater l'assurance du conjoint
        if (isset($data['assure']) && (strpos($data['assure'], 'couple') !== false)) {
            $formattedData['assurerVotreConjoint'] = "OUI";
        } else {
            $formattedData['assurerVotreConjoint'] = "NON";
            $formattedData['dateNaissanceConjoint'] = null; // Si le conjoint n'est pas assuré, on met la date de naissance à null
        }

        return $formattedData;
    }

    public function getFilteredAndOrderedTariffs(array $responseData, $age, $budget)
    {
        $formules = Config::get('formulesParticuliers.sante.formules_par_produit');
        $formuleBudget = Config::get('formulesParticuliers.sante.formule_budget');
        $ageRanges = Config::get('formulesParticuliers.sante.age_par_produit');
        $garanties = Config::get('formulesParticuliers.sante.garanties_par_formule');

        // Filter out objects with messageErreur that has a value or tarif is null
        $filteredData = array_filter($responseData, function ($item) {
            return (!isset($item['messageErreur']) || empty($item['messageErreur'])) && isset($item['tarif']) && $item['tarif'] !== null;
        });

        $compatibleFormules = [];
        $allTariffs = [];

        foreach ($filteredData as $item) {
            $produit = $this->getKeyByValue($formules, $item['formule']);
            if (!$produit) {
                continue; // Skip if produit is not found
            }
            $ageAutorise = $ageRanges[$produit];
            $isAgeCompatible = $this->isAgeBetween($age, $ageAutorise['min'], $ageAutorise['max']);
            $isBudgetCompatible = in_array($item['formule'], $formuleBudget[$budget]);

            $formuleData = [
                'produit' => $produit,
                'formule' => $item['formule'],
                'tarif' => $item['tarif'],
                'garanties' => $garanties[$item['formule']] ?? []
            ];

            if ($isAgeCompatible && $isBudgetCompatible) {
                $compatibleFormules[] = $formuleData;
            }
            $allTariffs[] = $formuleData;
        }

        // Sort compatible formules by tarif in ascending order
        usort($compatibleFormules, function ($a, $b) {
            return $a['tarif'] <=> $b['tarif'];
        });

        // Sort all tariffs by tarif in ascending order
        usort($allTariffs, function ($a, $b) {
            return $a['tarif'] <=> $b['tarif'];
        });

        // Get the top 3 compatible formules with the lowest tariffs
        $top3CompatibleFormules = array_slice($compatibleFormules, 0, 3);

        return [
            'top3_compatible_formules' => $top3CompatibleFormules,
            'all_tariffs' => $allTariffs
        ];
    }

    protected function getKeyByValue($array, $value)
    {
        foreach ($array as $key => $val) {
            if (in_array($value, $val)) {
                return $key;
            }
        }
        return null;
    }

    protected function isAgeBetween($age, $min, $max)
    {
        return $age >= $min && $age <= $max;
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
