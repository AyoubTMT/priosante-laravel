<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\TarificationNotificationMail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function sendEmailTest(Request $request)
    {

        $details = [
            'name' => 'name',
            'telephone' => 'telephone',
            'email' => 'email',
            'lien' => 'lien',
            'reference' => 'reference',
        ];

        $fromAddress = 'contact@prioritesantemutuelle.fr';
        Mail::to('contact@prioritesantemutuelle.fr')->send(new ContactMail($details, $fromAddress));

        return response()->json(['message' => 'Email sent successfully!']);
    }

    /**
     * Send a tarification notification email.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTarificationNotification(Request $request)
    {
        Log::info('Tarification notification request data:', $request->all());
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone'),
            'dateNaissance' => $request->input('dateNaissance'),
            'codePostal' => $request->input('codePostal'),
            'numeroSS' => $request->input('numeroSS'),
            'situationFam' => $request->input('situationFam'),
            'profession' => $request->input('profession'),
            'revenuMensuel' => $request->input('revenuMensuel'),
            'voie' => $request->input('voie'),
            'ville' => $request->input('ville'),
            'souscripteurIsAssure' => $request->input('souscripteurIsAssure')
        ];
        Log::info('Tarification notification data:', $data);
        Mail::send('mails.tarification_notification', $data, function($message) use ($data) {
            $message->to('contact@prioritesantemutuelle.fr')
                    ->subject('Notification de Tarification par le Client : ' . $data['name']);
        });
        Log::info('Tarification notification email sent successfully.');
        return response()->json(['message' => 'E-mail de notification envoyé avec succès!']);
    }
    /**
     * Send a subscription notification email.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSouscriptionNotification(Request $request)
    {
        $data = $request->all();

        Mail::send('mails.souscription_notification', $data, function($message) use ($data) {
            $message->to('contact@prioritesantemutuelle.fr')
                    ->subject('Notification de Souscription par le Client : ' . $data['cv'] .' '. $data['name']);
        });

        return response()->json(['message' => 'E-mail de notification envoyé avec succès!']);
    }
}
