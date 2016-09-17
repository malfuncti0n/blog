<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {
    //put your code here
    protected $table='comments';
    
    protected $fillable=[
        'user_id',
        'post_id',
        'content'   
    ];

    public function post(){
        return $this->belongsTo('App\Models\Post');
    }
}
