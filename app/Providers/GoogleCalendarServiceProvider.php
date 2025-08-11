<?php
namespace App\Providers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Google_Client::class, function ($app) {
            $client = new Google_Client(config('google-calendar.auth_config'));
            
            $client->setApplicationName('Sistem Bimbingan Akademik');
            $client->setClientId(config('google-calendar.client_id'));
            $client->setClientSecret(config('google-calendar.client_secret'));
            $client->setRedirectUri(config('google-calendar.redirect_uri'));
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');
            $client->setIncludeGrantedScopes(true);
            
            foreach(config('google-calendar.scopes') as $scope) {
                $client->addScope($scope);
            }

            // Jika menggunakan service account
            if (file_exists(storage_path('app/google-calendar/credentials.json'))) {
                $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
                if (config('google-calendar.calendar_id')) {
                    $client->setSubject(config('google-calendar.calendar_id'));
                }
            }

            return $client;
        });

        $this->app->singleton(Google_Service_Calendar::class, function ($app) {
            return new Google_Service_Calendar($app->make(Google_Client::class));
        });
    }
}