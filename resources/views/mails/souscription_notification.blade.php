<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de Souscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #467061;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 0 0 5px 5px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .client-info {
            background-color: #e7f4e8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nouvelle Souscription Effectuée</h1>
    </div>
    <div class="content">
        <p>Bonjour,</p>
        <p>Un client a effectué une souscription sur notre plateforme. Voici les détails :</p>

        <div class="client-info">
            <h2>Informations du Souscripteur</h2>
            <p><strong>Nom du Client :</strong> {{ $name }}</p>
            <p><strong>Email du Client :</strong> {{ $email }}</p>
            <p><strong>Téléphone du Client :</strong> {{ $telephone }}</p>
            <p><strong>Date de Naissance :</strong> {{ $dateNaissance }}</p>
            <p><strong>Code Postal :</strong> {{ $codePostal }}</p>
            <p><strong>Numéro de Sécurité Sociale :</strong> {{ $numeroSS }}</p>
            <p><strong>Situation Familiale :</strong> {{ $situationFam }}</p>
            <p><strong>Profession :</strong> {{ $profession }}</p>
            <p><strong>Revenu Mensuel :</strong> {{ $revenuMensuel }}</p>
            <p><strong>Voie :</strong> {{ $voie }}</p>
            <p><strong>Ville :</strong> {{ $ville }}</p>
            <p><strong>Civilité :</strong> {{ $cv }}</p>
            <p><strong>Souscripteur est Assuré :</strong> {{ $souscripteurIsAssure }}</p>
        </div>

        @if(isset($conjoint) && !empty($conjoint))
        <div class="client-info">
            <h2>Informations du Conjoint</h2>
            <p><strong>Nom du Conjoint :</strong> {{ $conjoint['nom'] }}</p>
            <p><strong>Prénom du Conjoint :</strong> {{ $conjoint['prenom'] }}</p>
            <p><strong>Date de Naissance du Conjoint :</strong> {{ $conjoint['dateNaissance'] }}</p>
            <p><strong>Numéro de Sécurité Sociale du Conjoint :</strong> {{ $conjoint['numeroSS'] }}</p>
            <p><strong>Code Organisme du Conjoint :</strong> {{ $conjoint['codeOrga'] }}</p>
        </div>
        @endif

        @if(isset($assure) && !empty($assure))
        <div class="client-info">
            <h2>Informations de l'Assuré</h2>
            <p><strong>Nom de l'Assuré :</strong> {{ $assure['nom'] }}</p>
            <p><strong>Prénom de l'Assuré :</strong> {{ $assure['prenom'] }}</p>
            <p><strong>Date de Naissance de l'Assuré :</strong> {{ $assure['dateNaissance'] }}</p>
            <p><strong>Numéro de Sécurité Sociale de l'Assuré :</strong> {{ $assure['numeroSS'] }}</p>
            <p><strong>Code Organisme de l'Assuré :</strong> {{ $assure['codeOrga'] }}</p>
        </div>
        @endif

        @if(isset($enfants) && !empty($enfants))
        <div class="client-info">
            <h2>Informations des Enfants</h2>
            @foreach($enfants as $enfant)
            <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                <p><strong>Nom de l'Enfant :</strong> {{ $enfant['nom'] }}</p>
                <p><strong>Prénom de l'Enfant :</strong> {{ $enfant['prenom'] }}</p>
                <p><strong>Date de Naissance de l'Enfant :</strong> {{ $enfant['dateNaissance'] }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <div class="client-info">
            <h2>Informations de Paiement</h2>
            <p><strong>IBAN pour le prélèvement :</strong> {{ $ibanPrelevemnt }}</p>
            <p><strong>IBAN de remboursement différent :</strong> {{ $ibanRembDifferent }}</p>
            <p><strong>IBAN de remboursement :</strong> {{ $ibanRemboursement }}</p>
            <p><strong>Mandat SEPA :</strong> {{ $mandatSepa }}</p>
            <p><strong>RUM :</strong> {{ $rum }}</p>
            <p><strong>Payeur différent :</strong> {{ $payeurDifferent }}</p>
            <p><strong>Nom du Payeur :</strong> {{ $nomPayeur }}</p>
            <p><strong>Prénom du Payeur :</strong> {{ $prenomPayeur }}</p>
            <p><strong>Numéro du Payeur :</strong> {{ $numeroPayeur }}</p>
            <p><strong>Type de Voie du Payeur :</strong> {{ $typeVoiePayeur }}</p>
            <p><strong>Voie du Payeur :</strong> {{ $voiePayeur }}</p>
            <p><strong>Bâtiment du Payeur :</strong> {{ $batimentPayeur }}</p>
            <p><strong>Libellé du Payeur :</strong> {{ $libellePayeur }}</p>
            <p><strong>Code Postal du Payeur :</strong> {{ $codePostalPayeur }}</p>
            <p><strong>Ville du Payeur :</strong> {{ $villePayeur }}</p>
        </div>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} PrioritéSantéMutuelle. Tous droits réservés.</p>
    </div>
</body>
</html>
