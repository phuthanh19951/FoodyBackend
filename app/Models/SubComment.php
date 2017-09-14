<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubComment extends Model
{
  use SoftDeletes;

  protected $table = "tbl_sub_comment";

  public $timestamps = true;

  protected $dates = ['deleted_at'];

  protected $fillable = [
      "content",
      "comment_id",
      "user_id"
  ];

  public function User(){
   return $this->belongsTo('App\Models\User');
  }

}
