<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    //put your code here
    protected $table='tags';
    
    protected $fillable=[
        'post_id',
        'value',
        'slug'
    ];
    
    public function posts(){
        return $this->belongsTo('App\Models\Post');
    }
}
