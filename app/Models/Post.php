<?php



/**
 * Description of Post
 *
 * @author savvaniss
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    //put your code here
    protected $table='posts';
    
    protected $fillable=[
        'user_id',
        'title',
        'slug',
        'content'   
    ];
    
    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }
    
    public function tags(){
        return $this->hasMany('App\Models\Tag');
    }
    public function user(){
        return $this->hasOne('App\Models\User');
    }

    public function latestPost(){
       return $this->where('active',1)->orderBy('id','desc')->take(5)->get();
    }

//    public function countComment($post_id){
//        return $this->find($post_id)->comments->count();
//    }


}
