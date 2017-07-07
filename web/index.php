<?php

/**
 *    Twitter/Application wraps silex application.
 *
 *    A-lot of the code is self explanatory, hence the documentation is not extensive.
 */

use Symfony\Component\HttpFoundation\Request;
use Twitter\Application;
use Twitter\Pdo\PdoServiceProvider;
use Silex\Provider\AssetServiceProvider;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Twitter\Application();

//using debug
$app['debug'] = true;

$request = Request::create($_SERVER['REQUEST_URI'], 'REQUEST');


#####################################################################
#   service injection
$app['user.auth'] = function ($app) {
    return new Twitter\Auth($app);
};

#   service injection
$app['twitter.tweets'] = $app->protect(function (Application $app, $userID)    {

    return $app->getPosts($userID);
});


#####################################################################
#   service provider, database, pdo driver
$app->register(new PdoServiceProvider(),
    array(
        'pdo.dsn' => 'mysql:host=localhost;dbname=twitter',
        'pdo.username' => 'twitter',
        'pdo.password' => 'password',
    )
);

##   service provider, assets
$app->register(new AssetServiceProvider());

##   service provider, twig templates
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../templates',
));

##  service provider, session data
$app->register(new Silex\Provider\SessionServiceProvider());



#####################################################################
##                         routing
##   Login
$app->get('/login', function (Request $request) use ($app) {

    if( $user = $app['user.auth']->checkAuth($app, $request)) {

        $app['session']->set('message', '');
        $app['session']->set('user', array('username' => $user['handle'], 'userid' => $user['userid']));

        return $app->redirect('/account');
    }

    $app['session']->set('message', 'Invalid Login, try again.');

    return $app->redirect('/');

})->method('POST');


##   Logout
$app->get('/logout', function (Request $request) use ($app) {

    $app['session']->set('user', array());
    $app['session']->invalidate(1);

    return $app->redirect('/');
});

#   Account
$app->get('/account', function () use ($app) {
    if (!$user = $app['session']->get('user')) {
        return $app->redirect('/');
    }

    return $app['twig']->render('account.html.twig', array(
        'name' => $user['username'],
        'tweets' => $app['twitter.tweets']($app, $user['userid'])
    ));
});

#   post tweet
$app->get('/post', function (Twitter\Application $app , Request $request)  {

    $post = $request->get('chars');

    if (!$user = $app['session']->get('user')) {
        //user not authenticated
        return $app->json(array('error' => 'Your session appears to be invalid.'));
    }


    if(strlen($post) > 140) {
        //oops someone is trying to send more characters than it is allowed, lets just decline the request
        return $app->json(array('error' => 'Your tweet seems to larger than it is allowed.'));
    }

    if($app->savePost($post, $user['userid'])) {

        #supply tweet response to populate tweet on page using ajax.
        return $app->json(array('post' => $post, 'date' => date('M d')));
    }

    return $app->json(array('error' => 'Could not post tweet! :('));

})->method('POST');


#   default route
$app->get('/', function (Twitter\Application $app , Request $request)  {

    return $app['twig']->render('landing.html.twig', array(
        'message' => $app['session']->get('message')
    ));
});
#####################################################################


$app->run();



