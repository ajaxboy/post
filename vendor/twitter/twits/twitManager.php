<?php

use Silex\ServiceProviderInterface;
use Silex\Application;

use Twitter\Twits;

class TwitterServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['some_service.some_argument'])) {
            $argument = 'default-value';
        }
        else {
            $argument = $app['some_service.some_argument'];
        }

        $app['some_service'] = $app->share(function() use ($app) {
            return new TwitterService($argument);
        });
    }
}