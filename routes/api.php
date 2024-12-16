<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\OnfidoController;

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


$router->group(['prefix' => 'resources'], function () use ($router) {
    $router->get('countries', 'CountryController@index');
    $router->get('cities', 'CityController@index');
    $router->get('airports', 'AirportController@index');
});

/**************************** PayPal ROUTE *************************/
$router->group(['prefix' => 'paypal'], function () use ($router) {
    $router->get('success', ["as" => "paypal.success", "uses" => 'PaymentProvider\PayPalController@onSuccess']);
    $router->get('cancel', ["as" => "paypal.cancel", "uses" => 'PaymentProvider\PayPalController@onCancel']);
});

$router->group(['prefix'=>'stripe'], function () use ($router){
   $router->post('transfer','PaymentProvider\StripeController@transfer');
   $router->post('create-customer','PaymentProvider\StripeController@createCustomer');
   $router->post('get-customer','PaymentProvider\StripeController@getCustomer');
});

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('register', 'Auth\RegisterController@register');
    $router->post('login', 'Auth\LoginController@login');
    $router->post('password/forgot', 'Auth\ForgotPasswordController@forgotPassword');
    $router->post('password/reset', 'Auth\ResetPasswordController@resetPassword');
    $router->post('check/data', 'UserController@validateInput');
    $router->post('delete','Auth\RegisterController@accountDelete');
    $router->post('update-password', 'Auth\RegisterController@updatePassword');
});


$router->group(['middleware' => ['jwt']], function () use ($router) {
    $router->group(['prefix' => 'dashboard'], function () use ($router) {
        $router->group(['prefix' => 'user'], function () use ($router) {
            $router->post('list', 'DashboardController@users');
            //$router->post('list', 'DashboardController@users');
        });

        $router->group(['prefix' => 'trip'], function () use ($router) {
            $router->post('list', 'DashboardController@trips');
        });

        $router->group(['prefix' => 'parcel'], function () use ($router) {
            $router->post('list', 'DashboardController@parcels');
        });

        $router->group(['prefix' => 'booking'], function () use ($router) {
            $router->post('list', 'DashboardController@luggage');
        });

        $router->group(['prefix' => 'wallet'], function () use ($router) {
            $router->post('list', 'DashboardController@transactions');
        });

        $router->group(['prefix' => 'setting'], function () use ($router) {
            $router->post('update', 'SettingController@store');
            $router->post('detail', 'SettingController@index');
        });
    });

    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('onfido-create-applicant', 'OnfidoController@createApplicant');
        $router->post('logout', 'Auth\LoginController@logout');
        $router->post('update', 'UserController@updateProfile');
        $router->post('me', 'UserController@me');
        $router->post('logout', 'Auth\LoginController@logout');
        $router->post('store-notification', 'NotificationController@store');
        $router->post('get-notification', 'NotificationController@list');


        $router->group(['prefix' => 'support'], function () use ($router) {
            $router->post('/create', 'SupportController@send');
            $router->post('/tickets', 'SupportController@tickets');
            $router->post('detail', 'SupportController@ticketDetail');
            $router->post('fields', 'SupportController@fields');
            $router->post('conversations', 'SupportController@conversations');
            $router->post('mine', 'SupportController@mine');
        });

        $router->group(['prefix' => 'verify'], function () use ($router) {
            $router->post('/', 'UserController@verify');
            $router->post('me', 'UserController@verifyMe');
        });

        $router->group(['prefix' => 'notification'], function () use ($router) {
            $router->post('token/add', 'FcmController@store');
            $router->post('channel/add', 'NotificationChannelController@add');
        });

        $router->group(['prefix' => 'search'], function () use ($router) {
            $router->post('/', 'SearchHistoryController@search');
            $router->post('save', 'SearchHistoryController@saveHistory');
             $router->post('saveAlert', 'SearchHistoryController@saveAlert');
            $router->post('history', 'SearchHistoryController@history');
        });

        $router->group(['middleware' => ['verified']], function () use ($router) {
            $router->group(['prefix' => 'review'], function () use ($router) {
                $router->post('rating', 'TravelerRatingController@stars');
                $router->post('add/rating', 'TravelerRatingController@add');
                $router->post('/', 'TravelerReviewController@myReviews');
                $router->post('/sent', 'TravelerReviewController@mySentReviews');
                $router->post('add/comment', 'TravelerReviewController@add');
                $router->post('add/comment-rating', 'TravelerReviewController@push');
            });

            $router->group(['prefix' => 'messaging'], function () use ($router) {
                $router->post('conversations', 'ConversationController@index');
                $router->post('conversations/details', 'ConversationController@conversationDetails');
                $router->post('message/send', 'MessageController@send');
                $router->post('message/detail', 'MessageController@getMessage');
                $router->post('message/delete', 'MessageController@delete');
                $router->post('messages', 'MessageController@index');
            });

            $router->group(['prefix' => 'trip'], function () use ($router) {
                $router->post('mine', 'TripController@myTrips');
                $router->post('add', 'TripController@add');
                $router->post('edit', 'TripController@edit');
                $router->post('delete', 'TripController@delete');
                $router->post('popular', 'TripController@popular');

                $router->group(['prefix' => 'favorite'], function () use ($router) {
                    $router->post('/', 'TripUserFavoriteController@index');
                    $router->post('update', 'TripUserFavoriteController@update');
                    $router->post('check', 'TripUserFavoriteController@check');
                });
            });

            $router->group(['prefix' => 'book'], function () use ($router) {
                $router->post('now', 'LuggageRequestController@add');
                $router->post('requests', 'LuggageRequestController@myRequests');
                $router->post('detail', 'LuggageRequestController@detail');
                $router->post('accept-decline', 'LuggageRequestController@acceptOrDecline');
                $router->post('cancel', 'LuggageRequestController@cancellation'); // correction de l'erreur d'orthographe cancel pour cancellation
                $router->post('delivery/confirmation', 'LuggageRequestController@confirmDelivery');
                $router->post('delivery/request', 'LuggageRequestController@requestDelivery');
            });

            $router->group(['prefix' => 'parcel'], function () use ($router) {
                $router->post('mine', 'ParcelController@myParcels');
                $router->post('add', 'ParcelController@add');
                $router->post('edit', 'ParcelController@edit');
                $router->post('delete', 'ParcelController@delete');
                $router->post('recipients/mine', 'RecipientController@index');

                $router->group(['prefix' => 'favorite'], function () use ($router) {
                    $router->post('/', 'ParcelUserFavoriteController@index');
                    $router->post('update', 'ParcelUserFavoriteController@update');
                    $router->post('check', 'ParcelUserFavoriteController@check');
                });
            });

            /**************************** WALLET ROUTE *************************/
            $router->group(['prefix' => 'wallet'], function () use ($router) {
                $router->post('my-activity', 'WalletController@activities');
                $router->post('cash-in', "WalletController@payIn");
                $router->post('cash-out', "WalletController@payOut");
                $router->post('mobile-sms-verify', "PaymentProvider\MobileMoneyController@SMSConfirmation");
                $router->post('my-balance', 'WalletController@myBalance');
            });

            /**************************** WALLET ROUTE *************************/
            $router->group(['prefix' => 'stripe'], function () use ($router) {
                $router->post('create', 'PaymentProvider\StripeController@connectAccount');
            });

            /**************************** PAYMENT METHOD ROUTE *************************/
            $router->group(['prefix' => 'payment-methods'], function () use ($router) {
                $router->post('index', 'PaymentMethodeController@index');
                $router->post('add', 'PaymentMethodeController@add');
                $router->post('update', "PaymentMethodeController@update");
                $router->post('delete', "PaymentMethodeController@delete");
                $router->post('set_default', "PaymentMethodeController@setDefault");
            });

            /**************************** ACCOUNT SETTING ROUTE *************************/
            $router->group(['prefix' => 'account-setting'], function () use ($router) {
                $router->post('set', 'AccountSettingController@add');
            });

            $router->group(['prefix' => 'feedback'], function () use ($router) {
                $router->post('send', 'FeedbackController@send');
            });
        });

          $router->group(['prefix' => 'luggage'], function () use ($router) {
            $router->post('delete', 'LuggageRequestController@restore');
        });
    });
});
 
$router->get('id-verification/{token}','UserController@idVerification');

