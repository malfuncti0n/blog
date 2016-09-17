<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Respect\Validation\Validator as v;
use Cocur\Slugify\Slugify as slug;
use Carbon\Carbon as carbon;
use Illuminate\Database\Capsule\Manager as DB;

class PostController extends Controller
{

    public function index($request, $response)
    {   
        //ayta einai ta stoixei poy aforoun ta posts
        $post=Post::where('slug','=',$request->getAttribute('routeInfo')[2]['title'])->first();;
        $tags[$post->id]=Post::find($post->id)->tags;
        $users[$post->id]=User::where('id','=',$post->user_id)->get();

        //ayta einai ta stoixceia poy aforoun ta comments
        $commentCount[$post->id]=Post::find($post->id)->comments->count();
        $comments=Comment::where('post_id','=',$post->id)->get();
        $userPerComment=[];
        foreach ($comments as $comment){

            $userPerComment[$comment->id]=User::where('id','=',$comment->user_id)->get();

        }
        //twra vazoume kai ton session user sth metavlith
        // userPerComment wste na ektiposoume swsta to onoma toy sthn html
        $userPerComment['session']=User::where('id','=',$_SESSION['user'])->get();;

        $this->prepareSidebar();
//        $this->addViewParameters(array(
//                'users'=>$users,
//                'post',$post,
//                'commentCount',$commentCount,
//                'comments',$comments,
//                'userPerComment',$userPerComment,
//                'tags',$tags
//            ));

        $this->view->getEnvironment()->addGlobal('users',$users);
        $this->view->getEnvironment()->addGlobal('post',$post);
        $this->view->getEnvironment()->addGlobal('commentCount',$commentCount);
        $this->view->getEnvironment()->addGlobal('comments',$comments);
        $this->view->getEnvironment()->addGlobal('userPerComment',$userPerComment);
        $this->view->getEnvironment()->addGlobal('tags',$tags);

        return $this->view->render($response, 'post.twig');

    }






    public function getCreate($request, $response){
        $this->prepareSidebar();
        return $this->view->render($response,'post.create.twig');
    }








    public function postCreate($request, $response)
    {
        $validation = $this->validator->validate($request, [
           // ->emailAvailable()
            'title' => v::notEmpty(),
            'content' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('post.create'));
        }

        $slug=$this->slug->slugify($request->getParam('title'));
       $post = Post::create([
           'title' => $request->getParam('title'),
           'content' => $request->getParam('content'),
           'slug' => $slug,
           'user_id' =>$_SESSION['user']
       ]);
        $time=$this->carbon->now();
        $tags=explode(',',$request->getParam('tag'));
        $i=0;
        foreach($tags as $tag){
            $tagSlug=$this->slug->slugify(trim($tag));
            $data[$i] = array('post_id'=>$post->id, 'value'=>$tag, 'slug'=>$tagSlug, 'created_at'=>$time, 'updated_at'=>$time);
                $i++;
        }



        Tag::insert($data);


        $this->flash->addMessage('info', 'You post have been created!');


        return $response->withRedirect($this->router->pathFor('post.index',array('title' => $slug)));
    }
}
