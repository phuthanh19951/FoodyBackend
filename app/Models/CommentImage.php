<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentImage extends Model
{
  use SoftDeletes;

  protected $table = 'tbl_image_comment';

  public $timestamps = true;

  protected $fillable = [
      'name',
      'type',
      'size',
      'comment_id',
      'url_image'
  ];

}
