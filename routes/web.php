<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/dohone-callback', ["as"=>"dohone.callback","uses"=>"PaymentProvider\MobileMoneyController@onSuccess"]);

$router->get('/countries', function () {
    $countries = array();
    $dirLangFirst = null;
    foreach (scandir(public_path("private/data")) as $dirLang) {
        if (strlen($dirLang) == 2 && !str_contains($dirLang, ".")) {
            $data = json_decode(file_get_contents(public_path("private/data/$dirLang/countries.json")), true);
            if (empty($countries)) {
                $dirLangFirst = $dirLang;
                $countries = $data;
            } else {
                foreach ($data as $datum) {
                    foreach ($countries as $key => $country) {
                        if ($country['id'] == $datum['id']) {
                            try {
                                $countries[$key]['name'][$dirLang] = $datum["name"];
                            } catch (Exception | Throwable $exception) {
                                $countries[$key]['name'] = [$dirLangFirst => trim($country['name']), $dirLang => $datum['name']];
                            }
                            break;
                        }
                    }
                }
            }
        }
    }


    file_put_contents(public_path('private/multi-lang-countries.json'), json_encode($countries));

    $finalCountries = array();
    foreach (json_decode(file_get_contents(public_path('private/countries.json')), true) as $key => $item) {
        try {
            if (empty($item)) {
                continue;
            }
            $country['id'] = $key + 1;

            foreach ($countries as $c) {
                if ($item['code'] == strtoupper($c['alpha3'])) {
                    $country['name'] = json_encode($c['name']);
                    $country['alpha2'] = strtoupper($c['alpha2']);
                    break;
                }
            }
            $country['alpha3'] = $item['code'];
            $country['phone_code'] = $item['calling_code'];
            $country['currency'] = $item['currency'];
            $country['flag_svg'] = $item['flag'];

            if (!empty($country['currency'] and !empty($country['name']))) {
                array_push($finalCountries, $country);
            }
        } catch (Exception $exception) {
            dd($exception->getMessage(), $item);
        }
    }

    file_put_contents(public_path('private/final-countries.json'), json_encode($finalCountries));


    $cities = array();
    foreach (json_decode(file_get_contents(public_path('private/raw-city.json')), true) as $key => $item) {
        try {
            if (empty($item)) {
                continue;
            }
            $item['id'] = $key + 1;

            foreach ($finalCountries as $country) {
                if ($item['country_code'] == strtoupper($country['alpha2'])) {
                    $item['country_id'] = $country['id'];
                    break;
                }
            }
            $item['coordinates']=json_encode($item['coordinates']);
            unset($item['name_translations']);
            unset($item['cases']);
            unset($item['country_code']);
            if (!empty(  $item['country_id'])) {
                array_push($cities, $item);
            }


        } catch (Exception $exception) {
            dd($exception->getMessage(), $item);
        }
    }
    file_put_contents(public_path('private/final-cities.json'), json_encode($cities));


    $airports = array();
    foreach (json_decode(file_get_contents(public_path('private/raw-airports.json')), true) as $key => $item) {
        try {
            if (empty($item)) {
                continue;
            }
            $item['id'] = $key + 1;

            foreach ($cities as $city) {
                if ($item['city_code'] == strtoupper($city['code'])) {
                    $item['city_id'] = $city['id'];
                    break;
                }
            }
            $item['coordinates']=json_encode($item['coordinates']);
            unset($item['name_translations']);
            unset($item['city_code']);
            unset($item['country_code']);
            if ($item['iata_type'] == "airport" and !empty($item['city_id'])) {
                unset($item['iata_type']);
                array_push($airports, $item);
            }

        } catch (Exception $exception) {
            dd($exception->getMessage(), $item);
        }
    }
    file_put_contents(public_path('private/final-airports.json'), json_encode($airports));
    dd($finalCountries);
});


