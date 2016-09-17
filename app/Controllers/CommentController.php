<?php
/**
 * Created by PhpStorm.
 * User: savva
 * Date: 9/15/2016
 * Time: 6:09 PM
 */

namespace App\Controllers;

use Slim\Views\Twig as View;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use Respect\Validation\Validator as v;

class CommentController extends Controller
{
    public function postCommentCreate($request, $response){

        $slug= $request->getParam('post_slug');
        $validation = $this->validator->validate($request, [
            // ->emailAvailable()
            'content' => v::notEmpty(),
            'post_id'=>v::notEmpty(),
           'post_slug'=>v::notEmpty()

        ]);


        if ($validation->failed()) {
            $this->flash->addMessage('info', 'Something went wrong with your comment. Try again');
            return $response->withRedirect($this->router->pathFor('post.index',array('title' => $slug)));
        }
        if($request->getParam('content')=='Write your reply'){
            $this->flash->addMessage('info', 'Something went wrong with your comment. Try again');
            return $response->withRedirect($this->router->pathFor('post.index',array('title' => $slug)));
        }

        var_dump($request->getParam('content'));

        $comment=Comment::create(['user_id'=>$_SESSION['user'],'post_id'=>$request->getParam('post_id'),'content'=>$request->getParam('content')]);

        $this->flash->addMessage('info', 'Thank you for your reply');
        return $response->withRedirect($this->router->pathFor('post.index',array('title' => $slug)));
    }




}