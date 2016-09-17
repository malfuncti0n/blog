<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Database\Capsule\Manager as DB;

class HomeController extends Controller
{
    protected $pageLimit=7;


    public function index($request, $response)
    {   


        //pagination start backend
        $postNumber=Post::where('active',1)->get()->count();
        $postPages=$postNumber/$this->pageLimit;

        $currentPage=$request->getAttribute('routeInfo')[2]['number'];
       // var_dump($currentPage);

        if(is_null($currentPage) || $currentPage==1){
            $currentPage=1;
            $posts=Post::where('active',1)->orderBy('id', 'desc')->take($this->pageLimit)->get();
        }else{
            $postToSkip=($currentPage-1)*$this->pageLimit;
            $posts=Post::where('active',1)->orderBy('id', 'desc')->take($this->pageLimit)->skip($postToSkip)->get();
        }

        //pagination end for backend


        $users=[];
        //se ayth th loypa pernw symplhromatika stixia gia ta post
        //opos comments, user  kai tags
        foreach ($posts as $post){
           // var_dump($posts);
            //pernoume ta comments gia to kathe post kai ta vazoume sto array
             $commentsCount[$post->id]=Post::find($post->id)->comments->count();
             $tags[$post->id]=Post::find($post->id)->tags;
             $users[$post->id]=User::where('id','=',$post->user_id)->get();


        }



         $this->prepareSidebar();
       // $this->addViewParameters();

//
//        $this->addViewParameters(array(
//            'posts'=>$posts,
//            'commentsCount'=>$commentsCount,
//            'tags',$tags,
//            'users',$users,
//            'postPages',$postPages,
//            'currentPage',$currentPage
//        ));


        $this->view->getEnvironment()->addGlobal('posts',$posts);
        $this->view->getEnvironment()->addGlobal('commentsCount',$commentsCount);
        $this->view->getEnvironment()->addGlobal('tags',$tags);
        $this->view->getEnvironment()->addGlobal('users',$users);
        $this->view->getEnvironment()->addGlobal('postPages',$postPages);
        $this->view->getEnvironment()->addGlobal('currentPage',$currentPage);






        return $this->view->render($response, 'home.twig');
    }

    public function latestPosts(){

    }
}
