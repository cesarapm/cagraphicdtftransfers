<?php

namespace App\Helpers;

class MetaTags
{
    private static array $pages = [
        // Home
        '' => [
            'title' => 'CT Graphic DTF Transfers | Press-Ready DTF Rolls Made Easy',
            'description' => 'Professional DTF (Direct-to-Film) transfers with 24-hour turnaround. Premium quality, ultra-color 9-color printing (CMYK-RGBO+WHITE), and custom gang sheet builder. 100% customer satisfaction guaranteed.',
            'keywords' => 'DTF transfers, direct to film printing, DTF gang sheet, custom DTF prints, heat transfer printing, t-shirt printing, DTF rolls, ultra color DTF, CMYK RGBO printing, gang sheet builder, DTF transfers USA, fast DTF printing, premium DTF quality',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        // Heat Press Guide
        'heat-press-guide' => [
            'title' => 'Heat Press Guide for DTF Transfers | CA Graphic DTF',
            'description' => 'Master heat pressing DTF transfers with our comprehensive guide. Learn temperature settings, techniques, and pro tips for perfect results every time.',
            'keywords' => 'heat press guide, DTF transfers, heat pressing technique, temperature settings, direct-to-film',
            'image' => 'https://cagraphicdtftransfers.com/images/portadas/Heat-Press-Guide.webp',
        ],
        
        // Nosotros (About)
        'nosotros' => [
            'title' => 'About CA Graphic DTF Transfers | Premium DTF Printing',
            'description' => 'Learn about CA Graphic DTF Transfers. We specialize in high-quality direct-to-film printing with ultra-color 9-color technology and 24-hour turnaround.',
            'keywords' => 'about CA Graphic DTF, DTF company, direct-to-film printing company, custom printing services',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        // Contact
        'contact' => [
            'title' => 'Contact CA Graphic DTF Transfers | Get In Touch',
            'description' => 'Contact CA Graphic DTF Transfers for inquiries, custom orders, and support. We\'re here to help with all your DTF transfer needs.',
            'keywords' => 'contact DTF company, DTF support, custom printing contact, DTF transfers inquiry',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        // Gang Sheet Builder
        'gang-sheet-builder' => [
            'title' => 'Gang Sheet Builder | Create Custom DTF Layouts | CA Graphic DTF',
            'description' => 'Build your own custom gang sheets online with our easy-to-use gang sheet builder. Arrange designs efficiently and get high-quality DTF transfers.',
            'keywords' => 'gang sheet builder, custom gang sheets, DTF layout tool, design arranger, DTF transfers',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        // Policy Pages
        'privacy-policy' => [
            'title' => 'Privacy Policy | CA Graphic DTF Transfers',
            'description' => 'Read our privacy policy to understand how CA Graphic DTF Transfers collects, uses, and protects your personal information.',
            'keywords' => 'privacy policy, data protection, terms',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        'refund-policy' => [
            'title' => 'Refund Policy | CA Graphic DTF Transfers',
            'description' => 'Learn about our refund policy for DTF transfers. We stand behind our products with a satisfaction guarantee.',
            'keywords' => 'refund policy, returns, satisfaction guarantee',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        'shipping-policy' => [
            'title' => 'Shipping Policy | CA Graphic DTF Transfers',
            'description' => 'Get information about shipping options and delivery times for your DTF transfer orders. Fast and reliable shipping available.',
            'keywords' => 'shipping policy, delivery times, shipping options',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        'terms-of-service' => [
            'title' => 'Terms of Service | CA Graphic DTF Transfers',
            'description' => 'Review the terms of service for using CA Graphic DTF Transfers. Understand your rights and responsibilities.',
            'keywords' => 'terms of service, user agreement, terms',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
        
        // Auth Pages (no index)
        'login' => [
            'title' => 'Login | CA Graphic DTF Transfers',
            'description' => 'Login to your CA Graphic DTF Transfers account to access your orders and gang sheet builder.',
            'keywords' => 'login, sign in, customer account',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        'register' => [
            'title' => 'Create Account | CA Graphic DTF Transfers',
            'description' => 'Register a new account to get started with CA Graphic DTF Transfers and access our online tools.',
            'keywords' => 'register, sign up, create account',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        // Payment Pages
        'checkout/exito' => [
            'title' => 'Order Successful | CA Graphic DTF Transfers',
            'description' => 'Your order has been successfully placed. Check your email for order details and tracking information.',
            'keywords' => 'order confirmation, payment successful',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        'checkout/pendiente' => [
            'title' => 'Order Pending | CA Graphic DTF Transfers',
            'description' => 'Your order is pending confirmation. We\'ll send you updates via email.',
            'keywords' => 'order pending, payment processing',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        'checkout/error' => [
            'title' => 'Order Error | CA Graphic DTF Transfers',
            'description' => 'There was an error processing your order. Please try again or contact support.',
            'keywords' => 'order error, payment failed',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        // User Pages
        'mis-pedidos' => [
            'title' => 'My Orders | CA Graphic DTF Transfers',
            'description' => 'View your order history and track your DTF transfer orders.',
            'keywords' => 'my orders, order history, order tracking',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
            'index' => false,
        ],
        
        'default' => [
            'title' => 'CT Graphic DTF Transfers | Press-Ready DTF Rolls Made Easy',
            'description' => 'Professional DTF (Direct-to-Film) transfers with 24-hour turnaround. Premium quality, ultra-color 9-color printing (CMYK-RGBO+WHITE), and custom gang sheet builder. 100% customer satisfaction guaranteed.',
            'keywords' => 'DTF transfers, direct to film printing, DTF gang sheet, custom DTF prints, heat transfer printing, t-shirt printing, DTF rolls, ultra color DTF, CMYK RGBO printing, gang sheet builder, DTF transfers USA, fast DTF printing, premium DTF quality',
            'image' => 'https://cagraphicdtftransfers.com/og-image.png',
        ],
    ];

    public static function getMeta()
    {
        $path = trim(request()->getPathInfo(), '/');
        
        // Extraer el slug principal (sin query strings)
        $slug = explode('?', $path)[0];
        
        // Manejo de rutas dinámicas como /seguimiento-pedido/:orderNumber
        if (str_starts_with($slug, 'seguimiento-pedido/')) {
            return [
                'title' => 'Order Tracking | CA Graphic DTF Transfers',
                'description' => 'Track your DTF transfer order status in real-time. Get updates on your shipment.',
                'keywords' => 'order tracking, track order, shipment status',
                'image' => 'https://cagraphicdtftransfers.com/og-image.png',
                'index' => false,
            ];
        }
        
        // Buscar la página en la configuración
        $page = self::$pages[$slug] ?? self::$pages['default'];
        
        return $page;
    }

    public static function getTitle()
    {
        return self::getMeta()['title'];
    }

    public static function getDescription()
    {
        return self::getMeta()['description'];
    }

    public static function getKeywords()
    {
        return self::getMeta()['keywords'];
    }

    public static function getImage()
    {
        return self::getMeta()['image'];
    }
    
    public static function shouldIndex()
    {
        $meta = self::getMeta();
        return $meta['index'] ?? true; // Index by default unless explicitly set to false
    }
}
