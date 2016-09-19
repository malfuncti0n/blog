<?php
/**
 * Created by PhpStorm.
 * User: savva
 * Date: 9/16/2016
 * Time: 5:41 PM
 */

namespace App\Controllers;


use Slim\Views\Twig as View;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Capsule\Manager as DB;

class TagController extends Controller
{
    protected $pageLimit=15;

    public function index($request, $response){
        $allTags=Tag::groupBy('value')->orderBy(DB::raw('count(value)', 'DESC'))->get();

        //pagination start

        $tagNumber=Tag::get()->count();
        $tagPages=$tagNumber/$this->pageLimit;
        $currentPage=$request->getAttribute('routeInfo')[2]['number'];

        if(is_null($currentPage) || $currentPage==1){
            $currentPage=1;
            $tags=Tag::groupBy('value')->orderBy('id', 'desc')->take($this->pageLimit)->get();
        }else{
            $tagToSkip=($currentPage-1)*$this->pageLimit;
            $tags=Tag::groupBy('value')->orderBy('id', 'desc')->take($this->pageLimit)->skip($tagToSkip)->get();
        }



        //pagination end



        $this->prepareSidebar();
      //  $this->view->getEnvironment()->addGlobal('allTags',$allTags);
        $this->view->getEnvironment()->addGlobal('tags',$tags);
        $this->view->getEnvironment()->addGlobal('currentPage',$currentPage);
        $this->view->getEnvironment()->addGlobal('tagPages',$tagPages);
        return $this->view->render($response, 'tags.twig');
    }



    public function getTag($request, $response){
        $this->prepareSidebar();

        $tag=$request->getAttribute('routeInfo')[2]['value'];

        //pagination start backend
        $postNumber=DB::table('posts')
            ->join('tags', 'posts.id', '=', 'tags.post_id')
            ->where('tags.slug', '=', $tag)
            ->select('posts.*', 'tags.id', 'tags.value')
            ->get()->count();
         $postPages=$postNumber/$this->pageLimit;

        $currentPage=$request->getAttribute('routeInfo')[2]['number'];
        // var_dump($currentPage);

        if(is_null($currentPage) || $currentPage==1){
            $currentPage=1;
            $posts=DB::table('posts')
                ->join('tags', 'posts.id', '=', 'tags.post_id')
                ->where('tags.slug', '=', $tag)
                ->select('posts.*', 'tags.post_id', 'tags.value')
                ->orderBy('posts.id', 'desc')
                ->take($this->pageLimit)
                ->get();
        }else{
            $postToSkip=($currentPage-1)*$this->pageLimit;
            $posts=DB::table('posts')
                ->join('tags', 'posts.id', '=', 'tags.post_id')
                ->where('tags.slug', '=', $tag)
                ->select('posts.*', 'tags.post_id', 'tags.value')
                ->orderBy('posts.id', 'desc')
                ->take($this->pageLimit)
                ->skip($postToSkip)
                ->get();
        }

        //pagination end for backend



//        se ayth th loypa pernw symplhromatika stixia gia ta post
//        opos comments, user  kai tags
      //  $postModel=new Post;
        foreach ($posts as $post){
            //pernoume ta comments gia to kathe post kai ta vazoume sto array
            $commentsCount[$post->id]=Comment::where('post_id','=',$post->id)->count();

            $tags[$post->id]=Post::find($post->id)->tags;
            $users[$post->id]=User::where('id','=',$post->user_id)->get();


        }







        $this->view->getEnvironment()->addGlobal('tag',$tag);

        //views metavlites gia to pagination
        $this->view->getEnvironment()->addGlobal('posts',$posts);
        $this->view->getEnvironment()->addGlobal('commentsCount',$commentsCount);
        $this->view->getEnvironment()->addGlobal('tags',$tags);
        $this->view->getEnvironment()->addGlobal('users',$users);
        $this->view->getEnvironment()->addGlobal('postPages',$postPages);
        $this->view->getEnvironment()->addGlobal('currentPage',$currentPage);
        return $this->view->render($response,'tag.single.twig');

    }

}