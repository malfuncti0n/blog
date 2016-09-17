<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

//global routes
$app->get('/', 'HomeController:index')->setName('home');
$app->get('/view/posts/page/{number}', 'HomeController:index')->setName('home.page');
$app->get('/view/posts/single/{title}', 'PostController:index')->setName('post.index');

$app->get('/view/tags', 'TagController:index')->setName('tags');
$app->get('/view/tags/page/{number}', 'TagController:index')->setName('tags.page');
$app->get('/view/tags/single/{value}', 'TagController:getTag')->setName('tag.single');

//unauthenticate routes
$app->group('', function () {
    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');

    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

//authenticate routes
$app->group('', function () {
    //for authentications
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');

    //for post creation
    $this->get('/create/post', 'PostController:getCreate')->setName('post.create');
    $this->post('/create/post', 'PostController:postCreate');

    //the comment creation
    $this->post('/create/comment', 'CommentController:postCommentCreate')->setName('comment.create');


})->add(new AuthMiddleware($container));
