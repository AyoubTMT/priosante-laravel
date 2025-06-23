<?php

namespace App\Services;

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
        if (isset($data['nbrEnfant'])) {
            $formattedData['nbrEnfant'] = $data['nbrEnfant'];
        }

        // Formater les dates de naissance des enfants
        $dateNaissanceEnfants = [];
        for ($i = 1; $i <= 8; $i++) {
            if (isset($data["dateNaissanceEnfant{$i}"])) {
                $dateNaissanceEnfants[] = $this->formatDate($data["dateNaissanceEnfant{$i}"]);
            }
        }
        if (!empty($dateNaissanceEnfants)) {
            $formattedData['dateNaissanceEnfants'] = $dateNaissanceEnfants;
        }

        // Formater l'assurance du conjoint
        if (isset($data['assure']) && (strpos($data['assure'], 'couple') !== false)) {
            $formattedData['assurerVotreConjoint'] = "OUI";
        } else {
            $formattedData['assurerVotreConjoint'] = "NON";
        }

        return $formattedData;
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
