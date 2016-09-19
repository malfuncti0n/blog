<?php

namespace App\Controllers;


//gia to sidebar
use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Database\Capsule\Manager as DB;


class Controller
{
    protected $container, $recentPostLimit=5, $tagLimit=5, $recentCommentLimit=5;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
    //h sidebar mas apoteleite apo stoixeia poy den allazoun apo ta input tou user
    public function prepareSidebar(){

        //latest post for feed limit
        $post= new Post;
        $latestPosts=$post->latestPost();



        //ta top used tags
        $mostUsesTags=$this->topTags();
        // latest commends gia to block comment
        $latestComments=$this->latestComments();


        //twra pernw to post title apo to kathena apo ayta, gia na ftiaksw ta  links sto view kathos kai ton user
        foreach ($latestComments as $latestComment){
            $postUrl[$latestComment->post_id]=Post::where('id',$latestComment->post_id)->get();

            $recentCommentUser[$latestComment->user_id]=User::where('id','=',$latestComment->user_id)->get();

        }

        //ta dinoume sto view
        $this->addViewParameters(array('recentCommentUser'=>$recentCommentUser,'latestComments'=>$latestComments,'postUrl'=>$postUrl,'mostUsesTags'=>$mostUsesTags,'latestPosts'=>$latestPosts));

    }

    public function topTags(){
       // return Tag::groupBy('value')->orderBy(DB::raw('count(value)', 'DESC'))->take($this->tagLimit)->get();
//   $tags=DB::table('tags')
//            ->select('value,count(*) as times')->groupBy('value')->orderBy('times','desc')->take($this->tagLimit)->get;
        return Tag::select('value', DB::raw('COUNT(value) as count'))
            ->groupBy('value')
            ->orderBy('count', 'desc')
            ->take($this->tagLimit)->get();

    }

    public function latestComments(){
        return Comment::orderBy('id','desc')->take($this->recentCommentLimit)->get();
    }

    public function addViewParameters($params=[]){


        foreach ($params as $key => $param){
            $this->view->getEnvironment()->addGlobal($key,$param);
        }
        return true;
    }


}
