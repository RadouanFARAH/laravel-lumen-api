<?php
return [
    'certification_success' => [
        'email' => [
            'subject' => 'Your certification',
            'header' => 'You have achieved Verified Status!',
            'title' => 'Your Certification',
            'greeting' => 'Hello :name,',
            'body' => 'It’s official! We are pleased to inform you that you now have Verified Status on Luggin.'
        ],
        'sms' => 'Status verified! Thanks to the information provided, your profile has just been verified.',
        'push' => [
            'title' => 'Luggin',
            'body' => 'You have obtained verified status',
        ]
    ],
    'certification_failed' => [
        'email' => [
            'subject' => 'Your certification failed',
            'header' => 'Unable to Verify Your Profile!',
            'title' => 'Certification Failed',
            'greeting' => 'Hello :name,',
            'body' => 'The verification of your information has failed.',
            'button' => 'Retry Verification'
        ],
        'sms' => 'Status check failed! Using the information provided, your profile could not be verified. Please try again by clicking on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Your certification failed'
        ]
    ],
    'demande_enligne' => [
        'email' => [
            'subject' => 'Your request is online!',
            'header' => 'Your request is online!',
            'title' => 'Your request from :source to :destination',
            'body' => 'Hello :name, your request has been published. The flight information you provided has been successfully verified. You can now receive reservations from senders on the same flight as you.',
            'button' => 'View your request'
        ],
        'sms' => 'Your request is online! Your request has been published after verification of your flight information. To view your request, click on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Your request is online!'
        ]
    ],
    'demande_envoyee' => [
        'email' => [
            'subject' => 'Your request has been sent!',
            'header' => 'Your request has been sent!',
            'title' => 'Your request for the trip from :source to :destination',
            'body' => 'Hello :name, your request has been sent to :destinataire. The flight information you provided has been successfully verified. Now, wait for the traveler to respond to your request.',
            'button' => 'View your request'
        ],
        'sms' => 'Your request has been sent! Your request has been sent to $destinataire after your flight information has been verified. To view your request, click on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Your request has been sent!'
        ]
    ],
    'offre_envoyee' => [
        'email' => [
            'subject' => 'Your offer has been sent!',
            'header' => 'Your offer has been sent!',
            'title' => 'Your trip on :date from :source to :destination',
            'body' => 'Hello :name, we are pleased to inform you that your offer has been sent to the sender. The flight information you provided has been successfully verified. You can now receive reservations from this sender.',
            'button' => 'View your offer'
        ],
        'sms' => 'Your offer has been sent! Your offer has been sent to the sender after verification of your flight information. To view your ad click on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Your offer has been sent!'
        ]
    ],
    'trajet_enligne' => [
        'email' => [
            'subject' => 'Your announcement is online!',
            'header' => 'Your announcement is online!',
            'title' => 'Your trip on :date from :source to :destination',
            'body' => 'Hello :name, we are pleased to inform you that your trip has been published. The flight information you provided has been successfully verified. You can now receive reservations from senders on the same flight as you.',
            'button' => 'View your announcement'
        ],
        'sms' => 'Your announcement is online! Your trip has been published after your flight information has been verified. To view your ad click on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Your announcement is online!'
        ]
    ],
    'trajet_enligne_failed' => [
        'email' => [
            'subject' => 'Unable to verify your flight information!',
            'header' => 'Unable to verify your flight information!',
            'title' => 'Verification of your flight information failed!',
            'body' => 'Hello :name, Your ad for a transaction on the same flight has not been published because we could not verify the flight information you provided. Please check them and resubmit.',
            'button' => 'Retry Verification'
        ],
        'sms' => 'Verification of your flight information failed! Your ad has not been published. Please retry by clicking on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Unable to verify your flight information!'
        ]
    ],
    'demande_enligne_failed' => [
        'email' => [
            'subject' => 'Unable to verify your flight information!',
            'header' => 'Unable to verify your flight information!',
            'title' => 'Verification of your flight information failed!',
            'body' => 'Hello :name, Your request for a transaction on the same flight has not been published because we could not verify the flight information you provided. Please check them and resubmit.',
            'button' => 'Retry Verification'
        ],
        'sms' => 'Verification of your flight information failed! Your request has not been published. Please retry by clicking on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Unable to verify your flight information!'
        ]
    ],
    'demande_envoyee_failed' => [
        'email' => [
            'subject' => 'Unable to verify your flight information!',
            'header' => 'Unable to verify your flight information!',
            'title' => 'Verification of your flight information failed!',
            'body' => 'Hello :name, Your request for a transaction on the same flight has not been sent because we could not verify the flight information you provided. Please check them and resubmit.',
            'button' => 'Retry Verification'
        ],
        'sms' => 'Verification of your flight information failed! Your request has not been sent. Please retry by clicking on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Unable to verify your flight information!'
        ]
    ],
    'offre_envoyee_failed' => [
        'email' => [
            'subject' => 'Unable to verify your flight information!',
            'header' => 'Unable to verify your flight information!',
            'title' => 'Verification of your flight information failed!',
            'body' => 'Hello :name, Your request for a transaction on the same flight has not been sent because we could not verify the flight information you provided. Please check them and resubmit.',
            'button' => 'Retry Verification'
        ],
        'sms' => 'Verification of your flight information failed! Your request has not been sent. Please retry by clicking on the following link: :link',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Unable to verify your flight information!'
        ]
    ],
    'conditions_generales_updated' => [
        'header' => 'Updates to Our Terms and Privacy Policy',
        'greeting' => 'Hello :name,',
        'body' => 'We’re constantly working to improve your experience with Luggin. We also want to ensure our policies explain how our services work.',
        'summary' => 'Here’s a summary of the main updates:',
        'terms' => 'Please read our full <a href=":terms_link">terms and conditions</a> and <a href=":privacy_link">privacy policy</a> which will take effect on :date. You can also view our previous terms <a href=":previous_terms_link">here</a>.',
    ],
    'nouveau_message' => [
        'email' => [
            'title' => 'Trip from :departCity to :arriveCity: You have a new message',
            'header' => 'has sent you a message',
            'button' => 'Reply'
        ],
        'sms' => 'Journey to :arriveCity, :sender sent you a message: Can you transport me... Click on the following link to see the message: :link',
        'push' => [
            'title' => ':sender',
            'body' => ' :departCity -> :arriveCity '
        ]
    ],
    'welcome' => [
        'email' => [
            'subject' => 'Welcome to Luggin',
            'header' => 'Welcome to Luggin, :name!',
            'ready' => 'Are you ready? Let’s go',
            'description' => 'Luggin connects travelers and senders worldwide for secure, affordable, and convenient transactions. Simply open the app, add an offer or request, and enjoy the journey.',
            'button' => 'Get Started',
            'how_it_works' => 'How it works',
            'open_app' => 'Open the app',
            'open_app_description' => 'Earn money while traveling by utilizing unused weight and offering a route.',
            'make_reservation' => 'Make a reservation',
            'make_reservation_description' => 'Save money on parcel delivery by booking available routes or posting weight requests online.',
            'help_community' => 'Give back to the community',
            'help_community_description' => 'Through completed transactions, reduce carbon impact while meeting locals.'
        ],
        'sms' => '',
        'push' => [
            'title' => 'Luggin',
            'body' => 'Welcome to Luggin'
        ]
    ],
    'footer' => [
        'help' => 'Get Help',
        'unsubscribe' => 'Unsubscribe',
        'company_name' => 'Luggin SAS',
        'address' => [
            'line1' => '53 chemin de la flambère',
            'line2' => '31300 Toulouse',
        ],
        'website' => 'luggin.co',
        'privacy' => 'Privacy Policy',
        'terms' => 'Terms and Conditions',
    ],
];
