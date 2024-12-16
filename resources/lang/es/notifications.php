<?php
return [
    'certification_success' => [
        'email' => [
            'subject' => 'Su certificación',
            'header' => '¡Has logrado el estado verificado!',
            'title' => 'Tu Certificación',
            'greeting' => 'Hola :name,',
            'body' => '¡Es oficial! Nos complace informarte que ahora tienes el estado verificado en Luggin.'
        ],
        'sms' => '¡Estado verificado! Gracias a la información facilitada, tu perfil acaba de ser verificado.',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Ha obtenido el estatus de verificado'
        ]
    ],
    'certification_failed' => [
        'email' => [
            'subject' => 'fallo de su certificación',
            'header' => '¡No se pudo verificar tu perfil!',
            'title' => 'Certificación Fallida',
            'greeting' => 'Hola :name,',
            'body' => 'La verificación de tu información ha fallado.',
            'button' => 'Reintentar Verificación'
        ],
        'sms' => '¡Comprobación de estado fallida! Con la información proporcionada, su perfil no ha podido ser verificado. Por favor, inténtelo de nuevo haciendo clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'fallo de su certificación'
        ]
    ],
    'demande_enligne' => [
        'email' => [
            'subject' => '¡Tu anuncio está en línea!',
            'header' => '¡Tu anuncio está en línea!',
            'title' => 'Tu solicitud de :source a :destination',
            'body' => 'Hola :name, tu solicitud ha sido publicada. La información de vuelo que proporcionaste ha sido verificada con éxito. Ahora puedes recibir reservas de remitentes en el mismo vuelo que tú.',
            'button' => 'Ver tu solicitud'
        ],
        'sms' => 'Su solicitud está en línea Su solicitud ha sido publicada tras verificar los datos de su vuelo. Para ver su solicitud, haga clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡Tu anuncio está en línea!'
        ]
    ],
    'demande_envoyee' => [
        'email' => [
            'subject' => '¡Tu solicitud ha sido enviada!',
            'header' => '¡Tu solicitud ha sido enviada!',
            'title' => 'Tu solicitud para el viaje de :source a :destination',
            'body' => 'Hola :name, tu solicitud ha sido enviada a :destinataire. La información de vuelo que proporcionaste ha sido verificada con éxito. Ahora, espera a que el viajero responda a tu solicitud.',
            'button' => 'Ver tu solicitud'
        ],
        'sms' => 'Su solicitud ha sido enviada Su solicitud ha sido enviada a $destinataire después de que los detalles de su vuelo hayan sido verificados. Para ver su solicitud, haga clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡Tu solicitud ha sido enviada!'
        ]
    ],
    'offre_envoyee' => [
        'email' => [
            'subject' => '¡Tu oferta ha sido enviada!',
            'header' => '¡Tu oferta ha sido enviada!',
            'title' => 'Tu viaje el :date de :source a :destination',
            'body' => 'Hola :name, nos complace informarte que tu oferta ha sido enviada al remitente. La información de vuelo que proporcionaste ha sido verificada con éxito. Ahora puedes recibir reservas de este remitente.',
            'button' => 'Ver tu oferta'
        ],
        'sms' => 'Su oferta ha sido enviada. Su oferta ha sido enviada al remitente una vez comprobados los datos de su vuelo. Para ver su anuncio haga clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡Tu oferta ha sido enviada!'
        ]
    ],
    'trajet_enligne' => [
        'email' => [
            'subject' => '¡Tu anuncio está en línea!',
            'header' => '¡Tu anuncio está en línea!',
            'title' => 'Tu viaje el :date de :source a :destination',
            'body' => 'Hola :name, nos complace informarte que tu viaje ha sido publicado. La información de vuelo que proporcionaste ha sido verificada con éxito. Ahora puedes recibir reservas de remitentes en el mismo vuelo que tú.',
            'button' => 'Ver tu anuncio'
        ],
        'sms' => '¡Su anuncio está en línea! Su viaje ha sido publicado después de comprobar los detalles de su vuelo. Para ver su anuncio haga clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡Tu anuncio está en línea!'
        ]
    ],
    'trajet_enligne_failed' => [
        'email' => [
            'subject' => '¡No se pudo verificar su información de vuelo!',
            'header' => '¡No se pudo verificar su información de vuelo!',
            'title' => '¡Fallo en la verificación de su información de vuelo!',
            'body' => 'Hola :name, Su anuncio para una transacción en el mismo vuelo no se ha publicado porque no pudimos verificar la información de vuelo que proporcionó. Por favor, verifíquela y vuelva a enviarla.',
            'button' => 'Reintentar verificación'
        ],
        'sms' => '¡Fallo en la verificación de su información de vuelo! Su anuncio no se ha publicado. Por favor, reintente haciendo clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡No se pudo verificar su información de vuelo!'
        ]
    ],
    'demande_enligne_failed' => [
        'email' => [
            'subject' => '¡No se pudo verificar su información de vuelo!',
            'header' => '¡No se pudo verificar su información de vuelo!',
            'title' => '¡Fallo en la verificación de su información de vuelo!',
            'body' => 'Hola :name, Su solicitud para una transacción en el mismo vuelo no se ha publicado porque no pudimos verificar la información de vuelo que proporcionó. Por favor, verifíquela y vuelva a enviarla.',
            'button' => 'Reintentar verificación'
        ],
        'sms' => '¡Fallo en la verificación de su información de vuelo! Su solicitud no se ha publicado. Por favor, reintente haciendo clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡No se pudo verificar su información de vuelo!'
        ]
    ],
    'demande_envoyee_failed' => [
        'email' => [
            'subject' => '¡No se pudo verificar su información de vuelo!',
            'header' => '¡No se pudo verificar su información de vuelo!',
            'title' => '¡Fallo en la verificación de su información de vuelo!',
            'body' => 'Hola :name, Su solicitud para una transacción en el mismo vuelo no se ha enviado porque no pudimos verificar la información de vuelo que proporcionó. Por favor, verifíquela y vuelva a enviarla.',
            'button' => 'Reintentar verificación'
        ],
        'sms' => '¡Fallo en la verificación de su información de vuelo! Su solicitud no se ha enviado. Por favor, reintente haciendo clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡No se pudo verificar su información de vuelo!'
        ]
    ],
    'offre_envoyee_failed' => [
        'email' => [
            'subject' => '¡No se pudo verificar su información de vuelo!',
            'header' => '¡No se pudo verificar su información de vuelo!',
            'title' => '¡Fallo en la verificación de su información de vuelo!',
            'body' => 'Hola :name, Su solicitud para una transacción en el mismo vuelo no se ha enviado porque no pudimos verificar la información de vuelo que proporcionó. Por favor, verifíquela y vuelva a enviarla.',
            'button' => 'Reintentar verificación'
        ],
        'sms' => '¡Fallo en la verificación de su información de vuelo! Su solicitud no se ha enviado. Por favor, reintente haciendo clic en el siguiente enlace: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => '¡No se pudo verificar su información de vuelo!'
        ]
    ],
    'conditions_generales_updated' => [
        'header' => 'Actualizaciones de nuestros Términos y Política de Privacidad',
        'greeting' => 'Hola :name,',
        'body' => 'Estamos trabajando constantemente para mejorar su experiencia con Luggin. También queremos asegurarnos de que nuestras políticas expliquen claramente cómo funcionan nuestros servicios.',
        'summary' => 'Aquí tiene un resumen de las principales actualizaciones:',
        'terms' => 'Por favor, lea nuestros <a href=":terms_link">términos y condiciones</a> y nuestra <a href=":privacy_link">política de privacidad</a>, que entrarán en vigor el :date. También puede consultar nuestros términos anteriores <a href=":previous_terms_link">aquí</a>.',
    ],
    'nouveau_message' => [
        'email' => [
            'title' => 'Viaje de :departCity a :arriveCity: Tienes un nuevo mensaje',
            'header' => 'te ha enviado un mensaje',
            'button' => 'Responder',
        ],
        'sms' => 'Viaje a :arriveCity, :sender te ha enviado un mensaje: ¿Puedes transportarme... Haz clic en el siguiente enlace para ver el mensaje: :link',
        'push' => [
            'title' => ' :sender ',
            'body' => ' :departCity -> :arriveCity '
        ]
    ],
    'welcome' => [
        'email' => [
            'subject' => '¡Bienvenido a Luggin!',
            'header' => '¡Bienvenido a Luggin, :name!',
            'ready' => '¿Estás listo? Vamos allá',
            'description' => 'Luggin conecta viajeros y remitentes en todo el mundo para transacciones seguras, económicas y convenientes. Simplemente abre la aplicación, agrega una oferta o solicitud, y disfruta del viaje.',
            'button' => 'Comenzar',
            'how_it_works' => 'Cómo funciona',
            'open_app' => 'Abre la aplicación',
            'open_app_description' => 'Gana dinero mientras viajas utilizando peso no usado y ofreciendo una ruta.',
            'make_reservation' => 'Haz una reserva',
            'make_reservation_description' => 'Ahorra dinero en la entrega de paquetes reservando rutas disponibles o publicando solicitudes de peso en línea.',
            'help_community' => 'Devuelve a la comunidad',
            'help_community_description' => 'A través de transacciones completadas, reduce el impacto de carbono mientras conoces a locales.'
        ],
        'sms' => '',
        'push' => [
            'title' => 'Luggin',
            'body' => ''
        ]
    ],
    'footer' => [
        'help' => 'Obtener ayuda',
        'unsubscribe' => 'Darse de baja',
        'company_name' => 'Luggin SAS',
        'address' => [
            'line1' => '53 chemin de la flambère',
            'line2' => '31300 Toulouse',
        ],
        'website' => 'luggin.co',
        'privacy' => 'Política de privacidad',
        'terms' => 'Términos y condiciones',
    ],
];
