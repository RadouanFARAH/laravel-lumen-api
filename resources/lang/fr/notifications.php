<?php
return [
    'certification_success' => [
        'email' => [
            'subject' => 'Votre certification',
            'header' => 'Vous avez obtenu le statut vérifié!',
            'title' => 'Votre Certification',
            'greeting' => 'Bonjour :name,',
            'body' => 'Ça y est, nous avons le plaisir de vous informer que vous bénéficiez du statut vérifié sur Luggin.'
        ],
        'sms' => 'Statut vérifié! Grâce aux informations fournies, votre profil vient d’être vérifié',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Vous avez obtenu le statut vérifié',
        ]
    ],
    'certification_failed' => [
        'email' => [
            'subject' => 'Échec de votre certification',
            'header' => 'Impossible de vérifier votre profil!',
            'title' => 'Échec de votre certification',
            'greeting' => 'Bonjour :name,',
            'body' => 'La vérification des informations a échoué.',
            'button' => 'Réessayer la vérification'
        ],
        'sms' => 'Echec vérification de statut! Grâce aux informations fournies, votre profil n’a pas pu être vérifié.Veuillez réessayer en cliquant sur le lien suivant:  :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Échec de votre certification'
        ]
    ],
    'demande_enligne' => [
        'email' => [
            'subject' => 'Votre annonce est en ligne!',
            'header' => 'Votre annonce est en ligne!',
            'title' => 'Votre demande pour le trajet de :source vers :destination',
            'body' => 'Bonjour :name, nous vous informons que votre demande a été publiée. Les informations de vol que vous avez fournies ont été vérifiées avec succès. Vous pouvez dès à présent recevoir des réservations des expéditeurs sur le même vol que vous.',
            'button' => 'Voir votre demande'
        ],
        'sms' => 'Votre demande est en ligne! Votre demande a été publiée après la vérification de vos informations de vol. Pour voir votre demande cliquer sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Votre annonce est en ligne!'
        ]
    ],
    'demande_envoyee' => [
        'email' => [
            'subject' => 'Votre demande a été envoyée!',
            'header' => 'Votre demande a été envoyée!',
            'title' => 'Votre demande pour le trajet de :source vers :destination',
            'body' => 'Bonjour :name, votre demande a été envoyée à :destinataire. Les informations de vol que vous avez fournies ont été vérifiées avec succès. Attendez maintenant que le voyageur réponde à votre demande.',
            'button' => 'Voir votre demande'
        ],
        'sms' => 'Votre demande a été envoyée! Votre demande a été transmise à $destinataire après la vérification de vos informations de vol. Pour voir votre demande cliquez sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Votre demande a été envoyée!'
        ]
    ],
    'offre_envoyee' => [
        'email' => [
            'subject' => 'Votre offre a été envoyée',
            'header' => 'Votre offre a été envoyée',
            'title' => 'Votre trajet de :date de :source vers :destination',
            'body' => 'Bonjour :name, nous vous informons que votre offre a été envoyée à l’expéditeur. Les informations de vol que vous avez fournies ont été vérifiées avec succès. Vous pouvez dès à présent recevoir des réservations de cet expéditeur.',
            'button' => 'Voir votre annonce'
        ],
        'sms' => 'Votre offre a été envoyée! Votre proposition a été transmise à l’expéditeur après la vérification de vos informations de vol. Pour voir votre annonce cliquez sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Votre offre a été envoyée'
        ]
    ],
    'trajet_enligne' => [
        'email' => [
            'subject' => 'Votre annonce est en ligne!',
            'header' => 'Votre annonce est en ligne!',
            'title' => 'Votre trajet du :date de :source vers :destination',
            'body' => 'Bonjour :name, nous vous informons que votre trajet a été publié. Les informations de vol que vous avez fournies ont été vérifiées avec succès. Vous pouvez dès à présent recevoir des réservations des expéditeurs sur le même vol que vous.',
            'button' => 'Voir votre annonce'
        ],
        'sms' => 'Votre annonce est en ligne! Votre trajet a été publié après la vérification de vos informations de vol. Pour voir votre  annonce cliquez sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Votre annonce est en ligne!'
        ]
    ],
    'trajet_enligne_failed' => [
        'email' => [
            'subject' => 'Impossible de vérifier vos informations de vol!',
            'header' => 'Impossible de vérifier vos informations de vol!',
            'title' => 'Echec de vérification de vos informations de vol!',
            'body' => 'Bonjour :name, Votre annonce pour une transaction sur le même vol n’a pas été publiée car nous n’avons pas pu vérifier les informations de vol que vous avez fournies. Veuillez les vérifier et les soumettre à nouveau.',
            'button' => 'Réssayer la vérification'
        ],
        'sms' => 'Echec vérification de vos informations de vol! votre annonce n’a pas été publiée. Veuillez réessayer en cliquant sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Impossible de vérifier vos informations de vol!'
        ]
    ],
    'demande_enligne_failed' => [
        'email' => [
            'subject' => 'Impossible de vérifier vos informations de vol!',
            'header' => 'Impossible de vérifier vos informations de vol!',
            'title' => 'Echec de vérification de vos informations de vol!',
            'body' => 'Bonjour :name, Votre demande pour une transaction sur le même vol n’a pas été publiée car nous n’avons pas pu vérifier les informations de vol que vous avez fournies. Veuillez les vérifier et les soumettre à nouveau.',
            'button' => 'Réssayer la vérification'
        ],
        'sms' => 'Echec vérification de vos informations de vol! votre demande n’a pas été publiée. Veuillez réessayer en cliquant sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Impossible de vérifier vos informations de vol!'
        ]
    ],
    'demande_envoyee_failed' => [
        'email' => [
            'subject' => 'Impossible de vérifier vos informations de vol!',
            'header' => 'Impossible de vérifier vos informations de vol!',
            'title' => 'Echec de vérification de vos informations de vol!',
            'body' => 'Bonjour :name, Votre demande pour une transaction sur le même vol n’a pas été envoyée car nous n’avons pas pu vérifier les informations de vol que vous avez fournies. Veuillez les vérifier et les soumettre à nouveau.',
            'button' => 'Réssayer la vérification'
        ],
        'sms' => 'Echec vérification de vos informations de vol! votre demande n’a pas été envoyée. Veuillez réessayer en cliquant sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Impossible de vérifier vos informations de vol!'
        ]
    ],
    'offre_envoyee_failed' => [
        'email' => [
            'subject' => 'Impossible de vérifier vos informations de vol!',
            'header' => 'Impossible de vérifier vos informations de vol!',
            'title' => 'Echec de vérification de vos informations de vol!',
            'body' => 'Bonjour :name, Votre demande pour une transaction sur le même vol n’a pas été envoyée car nous n’avons pas pu vérifier les informations de vol que vous avez fournies. Veuillez les vérifier et les soumettre à nouveau.',
            'button' => 'Réssayer la vérification'
        ],
        'sms' => 'Echec vérification de vos informations de vol! votre demande n’a pas été envoyée. Veuillez réessayer en cliquant sur le lien suivant: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Impossible de vérifier vos informations de vol!'
        ]
    ],
    'conditions_generales_updated' => [
        'header' => 'Mises à jour de nos Conditions Générales et de notre Politique de Confidentialité',
        'greeting' => 'Bonjour :name,',
        'body' => 'Nous travaillons constamment pour améliorer votre expérience avec Luggin. Nous souhaitons également nous assurer que nos politiques expliquent clairement le fonctionnement de nos services.',
        'summary' => 'Voici un résumé des principales mises à jour :',
        'terms' => 'Veuillez lire l’intégralité de nos <a href=":terms_link">conditions générales</a> et de notre <a href=":privacy_link">politique de confidentialité</a> qui entreront en vigueur le :date. Vous pouvez également consulter nos précédentes conditions <a href=":previous_terms_link">ici</a>.',
    ],
    'nouveau_message' => [
        'email' => [
            'title' => 'Trajet de :departCity vers :arriveCity: Vous avez un nouveau message',
            'header' => 'vous a envoyé un message',
            'button' => 'Répondre',
        ],
        'sms' => 'Trajet vers :arriveCity :sender vous a envoyé un message: Pouvez-vous me transporter... Cliquez sur le lien suivant pour voir le message: :link',
        'push' => [
            'title' => ':sender',
            'body' => ' :departCity -> :arriveCity '
        ]
    ],
    'welcome' => [
        'email' => [
            'subject' => 'Bienvenue sur Luggin',
            'header' => 'Bienvenue sur Luggin, :name!',
            'ready' => 'Vous êtes prêts? Allons-y',
            'description' => 'Luggin vous met en contact avec des voyageurs et expéditeurs à travers le monde pour des transactions sécurisées, abordables et commodes. Ouvrez simplement l’application, ajoutez une offre ou une demande et profitez du trajet.',
            'button' => 'Commencer',
            'how_it_works' => 'Comment ça marche',
            'open_app' => 'Ouvrez l’application',
            'open_app_description' => 'Gagnez de l’argent quand vous voyagez, mettez à profit vos kilos inexploités en proposant un trajet.',
            'make_reservation' => 'Faites une réservation',
            'make_reservation_description' => 'Économisez de l’argent lors de l’acheminement d’un colis en réservant les trajets proposés ou en publiant une demande de kilos en ligne.',
            'help_community' => 'Rendez service à la communauté',
            'help_community_description' => 'Grâce aux transactions effectuées, réduisez l’impact carbone tout en rencontrant des locaux.'
        ],
        'sms' => '',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Bienvenue sur Luggin'
        ]
    ],
    'footer' => [
        'help' => 'Obtenir de l’aide',
        'unsubscribe' => 'Se désabonner',
        'company_name' => 'Luggin SAS',
        'address' => [
            'line1' => '53 chemin de la flambère',
            'line2' => '31300 Toulouse',
        ],
        'website' => 'luggin.co',
        'privacy' => 'Confidentialité',
        'terms' => 'Conditions générales',
    ],
];
