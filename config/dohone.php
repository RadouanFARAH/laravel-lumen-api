<?php
return [
    /*
     * Start transaction
     *
     * rLocale : le choix de la langue. fr ou en
     *
     * rOnly : Ceci est optionnel. Si vous souhaitez que votre API n’affiche que certains opérateurs,
     * vous pouvez préciser ces opérateurs ici. 1=MTN, 2=Orange, 3=Express Union, 5=Visa via UBA,
     * 10=Dohone, 14= Visa via Wari,15=Wari card,16=VISA/MASTERCARD, 17=YUP.
     */

    'merchantToken' => 'EP216F15453785243027800',
    'currency' => 'EUR',
    'projectName' => env('APP_NAME', "Dohone"),
    'projectLogo' => 'https://www.my-dohone.com/dohone/site/res/img/homepage-slider/dohone-logo.png',
    'endPage' => env('APP_URL', "http://localhost"),
    'payInNotifyPage' => env('APP_URL', "http://localhost"),
    'cancelPage' => env('APP_URL', "http://localhost"),
    'language' => 'fr',
    'method' => '1, 2, 3, 10, 17',
    'numberNotifs' => 5,
    'payInUrl' => env("DOHONE_SANDBOX",false) ? 'https://www.my-dohone.com/dohone-sandbox/pay' : 'https://www.my-dohone.com/dohone/pay',

    'payOutHashCode' => '4AB73EE62AAB765C368A27C65BC879',
    'payOutPhoneAccount' => '237695499969',
    'payOutNotifyPage' => env('APP_URL', "http://localhost"),
    'payOutUrl' => env("DOHONE_SANDBOX",false) ? 'https://www.my-dohone.com/dohone-sandbox/transfert' : 'https://www.my-dohone.com/dohone/transfert'
];
