<?php

namespace App\Services;

use App\Models\Setting;

class MercadoPagoConfig
{
    /**
     * Obtener el access token de Mercado Pago
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getAccessToken(): ?string
    {
        return Setting::get('mercadopago_access_token')
            ?? config('services.mercadopago.access_token');
    }

    /**
     * Obtener el webhook secret de Mercado Pago
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getWebhookSecret(): ?string
    {
        return Setting::get('mercadopago_webhook_secret')
            ?? config('services.mercadopago.webhook_secret');
    }

    /**
     * Obtener la URL de notificación de Mercado Pago
     * Primero intenta desde la BD, luego desde el .env
     */
    public static function getNotificationUrl(): ?string
    {
        return Setting::get('mercadopago_notification_url')
            ?? config('services.mercadopago.notification_url');
    }

    /**
     * Verificar si Mercado Pago está configurado
     */
    public static function isConfigured(): bool
    {
        return !empty(self::getAccessToken());
    }
}
