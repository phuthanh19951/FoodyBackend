<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;
use Response;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;

class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getProvince()
    {

      $response = [
          'provinceList' => '',
          'status'       => 1
      ];

      $provinces = DB::select('
        select count(tbl_post.province) as numberpost ,province.name , province.provinceid
        from province
        left join tbl_post
        on  province.provinceid = tbl_post.province
        where tbl_post.deleted_at is null
        group by province.provinceid
      ');

      if($provinces){
        $response['provinceList'] = $provinces;
      }else{
        $response['status'] = 0;
      }

      return \Response::json($response);
    }

    public function getCategoryPost(){

      $response = [
          'categoryList' => '',
          'status'       => 1
      ];

      $categoryPosts = DB::table('tbl_category')->get();

      if($categoryPosts){
        $response['categoryList'] = $categoryPosts;
      }else{
        $response['status'] = 0;
      }

      return \Response::json($response);
    }

    public function getDistrictByProvinceId($provinceId){

      $response = [
          'districtList' => '',
          'status'       => 1
      ];

      if(isset($provinceId) && is_numeric($provinceId)){
        $districtList = DB::table('district')->where('provinceid',$provinceId)->get();
        if($districtList){
          $response['districtList'] = $districtList;
        }else{
          $response['status'] = 0;
        }
      }

      return Response::json($response);
    }

    public function userLogin(){
      $response = [
          'status' => '401',
          'data'   => ''
      ];

      $content = [
          'email' => $_POST['email'],
          'password' => $_POST['password']
      ];

//      $checkRemember = $request->input('remember') ? true : false;

      if( Auth::guard('admin')->attempt($content,true) ){
        $response['status'] = 200;
        $response['data'] = User::where('email',$content['email'])->get();
      }

      return Response::json($response);

    }

    public function getCommentsModal(){

      $response = array(
          'status'     => 204,
          'data'       => ''
      );

      $response['data']['detail'] = Post::join('tbl_image','tbl_post.thumb_id','tbl_image.id')
          ->select($this->getColumnPost())
          ->find($_GET['id']);

      $response['data']['comment'] = Comment::with(array('User'=>function($query){
        $query->select('id','url_image','fullname');
      },'SubComments.User'=>function($query){
        $query->select('id','url_image','fullname');
      },'Images'=>function($query){
        $query->select('name','comment_id','url_image');
      }))->where('post_id',$_GET['id'])
          ->select('id','content','user_id','created_at')
          ->get();

      $response['data']['detail']['number_comment'] = count($response['data']['comment']);

      if(count($response['data']) > 0){
        $response['status'] = 200;
      }

      return \Response::json($response);

    }

  // Form column to select
  public function getColumnPost(){
    return array('tbl_post.id',
        'tbl_post.title',
        'tbl_post.address',
        'tbl_post.cnt_rank',
        'tbl_post.avg_mark_location',
        'tbl_post.avg_mark_quality',
        'tbl_post.avg_mark_serve',
        'tbl_post.avg_mark_space',
        'tbl_post.avg_mark_price',
        'tbl_image.url_image'
    );
  }

}
