<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
  use SoftDeletes;

  protected $table = "tbl_comment";

  public $timestamps = true;

  protected $fillable = [
    "content" ,
    "user_id",
    "post_id",
    "cnt_like"
  ];

  public function Post(){
    return $this->belongsTo('App\Models\Post');
  }

  public function User(){
    return $this->belongsTo('App\Models\User','user_id');
  }

  public function SubComments(){
    return $this->hasMany('App\Models\SubComment')->select(array('id','content','user_id','comment_id','created_at'));
  }

  public function Images(){
    return $this->hasMany('App\Models\CommentImage');

  }
}
