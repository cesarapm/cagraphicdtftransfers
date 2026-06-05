<?php

return [
    'Ejecutivo Comercial' => [
        [
            'pregunta' => '¿Cuál es tu Ticket Promedio de Venta?',
            'tipo' => 'opciones',
            'opciones' => [
                '50 mil',
                '50 y 250 mil',
                '250 a 500 mil',
                '500 mil a 1 millón',
                'Más de 1 millón',
            ],
        ],
        [
            'pregunta' => 'Mercado primario de venta',
            'tipo' => 'opciones',
            'opciones' => [
                'Categoria corporativo',
                'Industrial',
                'Residencial',
                'Pequeños negocios',
                'Institucional',
            ],
        ],
        [
            'pregunta' => 'Perfiles que prospecta son:',
            'tipo' => 'opciones',
            'opciones' => [
                'Vicepresidentes',
                'Dueños de negocio',
                'Directores Generales',
                'Gerencias',
                'Consumidor Final',
            ],
        ],
        [
            'pregunta' => 'Lo que vende su precio:',
            'tipo' => 'opciones',
            'opciones' => [
                'Categoria, generalmente el mas alto del mercado',
                'Competitivo, Esta por debajo del mercado.',
                'Valor agregado, no compite en precio',
                'Desconozco',
            ],
        ],
        [
            'pregunta' => 'Por lo general:',
            'tipo' => 'opciones',
            'opciones' => [
                'Yo llevo el proceso de Venta completo, Vendo y Paso la cuenta a otra área.',
                'Yo llevo el proceso de Venta completo, Vendo y Atiendo la cuenta.',
                'Yo participo en el proceso de Venta, pero no lo llevo completo.',
                'No participo en el proceso de Venta, solo atiendo la cuenta.',
            ],
        ],
        [
            'pregunta' => 'Mantengo la cuenta:',
            'tipo' => 'opciones',
            'opciones' => [
                'Yo mantengo la cuenta, renuevo, doy servicio y vendo más.',
                'Yo mantengo la cuenta, renuevo y doy servicio.',
                'Yo mantengo la cuenta, solo renuevo.',
                'No mantengo la cuenta, solo vendo.',
            ],
        ],
        [
            'pregunta' => 'A mi me gusta más:',
            'tipo' => 'opciones',
            'opciones' => [
                'Prospectar',
                'Armar Propuestas',
                'Resolver problemas con el cliente',
                'Analizar las cuentas',
                'Hacer el cierre',
                'Dar servicio postventa',
            ],
        ],
        [
            'pregunta' => 'Por lo general vendo:',
            'tipo' => 'opciones',
            'opciones' => ['Directo al usuario', 'Por medio de un tercero', 'Ambos'],
        ],
        [
            'pregunta' => 'Por lo general lo que vendo:',
            'tipo' => 'opciones',
            'opciones' => [
                'Los prospectos no han planeado comprarlo',
                'Estan ya en proceso de selección de proveedor',
                'Ni si quiera conocen el producto de lo que vendo.',
            ],
        ],
        [
            'pregunta' => '¿En los últimos dos años en que  porcentaje has  llegado  en tus metas?',
            'tipo' => 'opciones',
            'opciones' => ['100%', '80%', '60%', 'Nunca he llegado.'],
        ],
        [
            'pregunta' => 'Por lo general lo que vendo desde la primera cita al cierre me lleva:',
            'tipo' => 'opciones',
            'opciones' => ['Una llamada telefónica', 'Una semana', '1 mes', '3 meses', '+ de 6 meses'],
        ],
        [
            'pregunta' => '¿Aceptarías un esquema de pago 100% variable?',
            'tipo' => 'opciones',
            'opciones' => ['Si', 'No'],
        ],
        // [
        //     'pregunta' => '¿Manejas algún CRM? ¿Cuál?',
        //     'tipo' => 'texto',
        // ],
        // [
        //     'pregunta' => '¿Manejo de Excel?',
        //     'tipo' => 'opciones',
        //     'opciones' => ['Básico', 'Intermedio', 'Avanzado', 'No manejo'],
        // ],
    ],
    'Gerente Comercial' => [
        [
            'pregunta' => 'En mi última posición gerencial',
            'tipo' => 'opciones',
            'opciones' => ['Mantenía ventas personales', 'Administraba', 'Desarrollaba vendedores'],
        ],
        [
            'pregunta' => 'A mi equipo de ventas:',
            'tipo' => 'opciones',
            'opciones' => [
                'Tenia presupuesto para la capacitación con profesionales externos',
                'Yo los capacitaba',
                'Contaba con vendedores que no necesitaban capacitación',
                'No creo en la capacitación.',
            ],
        ],
        [
            'pregunta' => 'Mi equipo comercial más grande ha sido de:',
            'tipo' => 'opciones',
            'opciones' => ['1 a 5 personas', '5 a 10 personas', '10 a 50 personas', '+ de 50 personas'],
        ],
        // [
        //     'pregunta' => 'Tengo experiencia en la gestión del equipo a través de un CRM',
        //     'tipo' => 'opciones',
        //     'opciones' => ['Si', 'No'],
        // ],
        // [
        //     'pregunta' => 'Mi manejo del Excel es:',
        //     'tipo' => 'opciones',
        //     'opciones' => ['Básico', 'Intermedio', 'Avanzado', 'No manejo'],
        // ],
        [
            'pregunta' => 'Mi mayor reto comercial ha sido:',
            'tipo' => 'opciones',
            'opciones' => [
                'Mantener la calidad de las cuentas',
                'Defender las cuentas',
                'Crecer las cuentas',
                'Generar nuevas cuentas',
                'Todas las anteriores.',
            ],
        ],
        [
            'pregunta' => 'En mi experiencia he llegado a:',
            'tipo' => 'opciones',
            'opciones' => [
                'Diseñar la estrategia comercial',
                'Ejecutar la estrategia comercial',
                'Ambas',
                'Ninguna.',
            ],
        ],
        [
            'pregunta' => 'Respecto a la parte económica, cuáles de las siguientes opciones has gestionado:',
            'tipo' => 'opciones',
            'opciones' => ['Presupuestos', 'Cuotas', 'Incentivos', 'Todas las anteriores', 'Ninguna'],
        ],
        [
            'pregunta' => 'Me considero un jefe que hace:',
            'tipo' => 'opciones',
            'opciones' => [
                'Micromanagement',
                'Management',
                'Management por objetivos',
                'Management por valores',
                'No se que tipo de jefe soy',
            ],
        ],
        [
            'pregunta' => 'Me reúno con mi equipo:',
            'tipo' => 'opciones',
            'opciones' => ['Diario', 'Semanal', 'Mensual', 'Todas las anteriores', 'Rara vez'],
        ],
    ],
    'Director Comercial' => [
        [
            'pregunta' => 'En mi última posición en Dirección',
            'tipo' => 'opciones',
            'opciones' => ['Mantenía ventas personales', 'Administraba', 'Desarrollaba Gerentes'],
        ],
        [
            'pregunta' => 'A mi equipo Comercial:',
            'tipo' => 'opciones',
            'opciones' => [
                'Tenia presupuesto para la capacitación con profesionales externos',
                'Yo los capacitaba',
                'Contaba con vendedores que no necesitaban capacitación',
                'No creo en la capacitación.',
            ],
        ],
        [
            'pregunta' => 'Mi equipo comercial más grande ha sido de:',
            'tipo' => 'opciones',
            'opciones' => ['1 a 5 personas', '5 a 10 personas', '10 a 50 personas', '+ de 50 personas'],
        ],
        // [
        //     'pregunta' => 'Tengo experiencia en la gestión del equipo a través de un CRM',
        //     'tipo' => 'opciones',
        //     'opciones' => ['Si', 'No'],
        // ],
        // [
        //     'pregunta' => 'Mi manejo del Excel es:',
        //     'tipo' => 'opciones',
        //     'opciones' => ['Básico', 'Intermedio', 'Avanzado', 'No manejo'],
        // ],
        [
            'pregunta' => 'Mi mayor reto comercial ha sido:',
            'tipo' => 'opciones',
            'opciones' => [
                'Mantener la calidad de las cuentas',
                'Defender las cuentas',
                'Crecer las cuentas',
                'Generar nuevas cuentas',
                'Todas las anteriores.',
            ],
        ],
        [
            'pregunta' => 'En mi experiencia he llegado a:',
            'tipo' => 'opciones',
            'opciones' => [
                'Diseñar la estrategia comercial',
                'Ejecutar la estrategia comercial',
                'Ambas',
                'Ninguna.',
            ],
        ],
        [
            'pregunta' => 'Respecto a la parte económica, cuáles de las siguientes opciones has gestionado:',
            'tipo' => 'opciones',
            'opciones' => ['Presupuestos', 'Cuotas', 'Incentivos', 'Todas las anteriores', 'Ninguna'],
        ],
        [
            'pregunta' => 'Me considero un jefe que hace:',
            'tipo' => 'opciones',
            'opciones' => [
                'Micromanagement',
                'Management',
                'Management por objetivos',
                'Management por valores',
                'No se que tipo de jefe soy',
            ],
        ],
        [
            'pregunta' => 'Me reúno con mi equipo:',
            'tipo' => 'opciones',
            'opciones' => ['Diario', 'Semanal', 'Mensual', 'Todas las anteriores', 'Rara vez'],
        ],
    ],
];
