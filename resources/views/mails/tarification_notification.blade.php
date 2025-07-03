<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de Tarification</title>
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
            background-color: #4CAF50;
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
        <h1>Nouvelle Tarification Effectuée</h1>
    </div>
    <div class="content">
        <p>Bonjour,</p>
        <p>Un client a effectué une tarification sur notre plateforme. Voici les détails :</p>

        <div class="client-info">
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
            <p><strong>Souscripteur est Assuré :</strong> {{ $souscripteurIsAssure }}</p>
        </div>

    </div>
    <div class="footer">
        <p>©{{ date('Y') }} PrioriteSanteMutuelle.</p>
    </div>
</body>
</html>
