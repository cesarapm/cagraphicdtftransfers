<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Log::info('Contact form submission received', $request->all());
        
        // Validar los datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'comment' => 'required|string|min:10',
        ]);

        try {
            // Log::info('Starting to send email');
            
            // Enviar email al contacto configurado en .env
            $contactEmail = config('mail.contact_email');
            // Log::info('Contact email config: ' . $contactEmail);
            
            Mail::to($contactEmail)
                ->send(new ContactMail(
                    $validated['name'],
                    $validated['email'],
                    $validated['phone'] ?? '',
                    $validated['comment']
                ));

            // Log::info('Contact email sent successfully to ' . $contactEmail);

            return response()->json([
                'message' => 'Message sent successfully'
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Contact email error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Error sending message: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }
}
