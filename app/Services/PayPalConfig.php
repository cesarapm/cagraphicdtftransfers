<?php

namespace App\Services;

use App\Models\Setting;

class PayPalConfig
{
    /**
     * Obtener el Client ID de PayPal
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getClientId(): ?string
    {
        return Setting::get('paypal_client_id')
            ?? config('services.paypal.client_id');
    }

    /**
     * Obtener el Client Secret de PayPal
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getClientSecret(): ?string
    {
        return Setting::get('paypal_client_secret')
            ?? config('services.paypal.client_secret');
    }

    /**
     * Obtener el modo de operación (sandbox/live)
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getMode(): string
    {
        return Setting::get('paypal_mode', 'sandbox')
            ?? config('services.paypal.mode', 'sandbox');
    }

    /**
     * Obtener la URL base de la API según el modo
     */
    public static function getApiUrl(): string
    {
        return self::getMode() === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Verificar si PayPal está configurado
     */
    public static function isConfigured(): bool
    {
        return !empty(self::getClientId()) && !empty(self::getClientSecret());
    }

    /**
     * Obtener un access token de PayPal
     */
    public static function getAccessToken(): ?string
    {
        if (!self::isConfigured()) {
            return null;
        }

        $url = self::getApiUrl() . '/v1/oauth2/token';
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? null;
        }

        return null;
    }
}
