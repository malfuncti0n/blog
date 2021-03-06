<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

require __DIR__ . '/../vendor/autoload.php';

use Respect\Validation\Validator as v;

//configuration values
use Noodlehaus\Config;

session_start();
$config = new Config(__DIR__ . '/../app/config');



$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => $config->get('mysql.driver'),
            'host' => $config->get('mysql.host'),
            'database' => $config->get('mysql.database'),
            'username' => $config->get('mysql.username'),
            'password' => $config->get('mysql.password'),
            'charset' =>  $config->get('mysql.charset'),
            'collation' => $config->get('mysql.collation'),
            'prefix' => $config->get('mysql.prefix'),
        ]
    ],
]);


$container = $app->getContainer();

//$container['config'] = function ($container) {
//    return new \Noodlehaus\Config($conf = new Config(__DIR__ . '/config'));
//};


$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
    
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};



$container['auth'] = function ($container) {
    return new \App\Auth\Auth;
};

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

$container['slug'] = function ($container) {
    return new \Cocur\Slugify\Slugify;
};
//testing config files



$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
            //enable debug
        'debug' => true
    ]);
    //enable debug
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    
    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};

$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['PostController'] = function ($container) {
    return new \App\Controllers\PostController($container);
};

$container['CommentController'] = function ($container){
    return new \App\Controllers\CommentController($container);
};

$container['PasswordController'] = function ($container) {
    return new \App\Controllers\Auth\PasswordController($container);
};

$container['TagController'] = function ($container) {
    return new \App\Controllers\TagController($container);
};


$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

$container['carbon']= function ($container){
    return new \Carbon\Carbon;
};

$container['rss']= function ($container){
    return new \FeedWriter\RSS2;
};


//$container['mail']= function ($container){
//    return new PHPMailer;
//};
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require __DIR__ . '/../app/routes.php';
