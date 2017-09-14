<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Image;
use App\Models\Comment;
use App\Models\Food;
use App\Models\SubComment;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
      $response = [
          "postList" => "",
          "status"   => 1
      ];

      // Declared string for query .
      $queryString = " where table2.deleted_at is null ";

      // Check $_GET global variable not empty.
      // If it is not empty , we will create a string with key and value
      if(!empty($_GET)){
        $queryString .= $this->queryParams($_GET,'table2');
      }

      $postList = DB::select('
         select
          table2.id ,
          table2.title ,
          table2.address ,
          table2.cnt_rank ,
          table2.content ,
          table2.comments ,
          table2.images ,
          table2.fullname,
          table2.district,
          table2.province,
          table2.category_id,
          table2.url_image as avatar,
          table2.deleted_at,
          table2.avg_food_price,
          tbl_image.url_image as post_image
            from(
              SELECT *
                FROM (
                 SELECT
                   tbl_post.id,
                   title,
                   tbl_post.address,
                   cnt_rank,
                   thumb_id,
                   content,
                   comments,
                   fullname,
                   url_image,
                   district,
                   province,
                   category_id,
                   tbl_post.avg_food_price,
                   tbl_post.deleted_at
                   FROM tbl_post
                   LEFT JOIN (
                    SELECT *
                    FROM tbl_users
                    INNER JOIN (
                     SELECT
                       content,
                       user_id,
                       tbl_comment.post_id,
                       comments
                       FROM tbl_comment
                       INNER JOIN (
                         SELECT
                          max(created_at) AS a,
                          count(post_id)  AS comments,
                          post_id
                          FROM tbl_comment
                          GROUP BY post_id
                          ) subcomment
                          ON subcomment.post_id = tbl_comment.post_id AND
                          subcomment.a = tbl_comment.created_at
                           ) comment ON comment.user_id = tbl_users.id
                             ) comment ON tbl_post.id = comment.post_id
                                ) AS table1 INNER JOIN (
                                     SELECT
                                      count(post_id) AS images,
                                      post_id
                                      FROM tbl_image
                                      GROUP BY post_id
                                 ) image ON table1.id = image.post_id
                 ) as table2 left join tbl_image on table2.thumb_id = tbl_image.id ' .$queryString. ' 
          ');

      if($postList){
        $response['postList'] = $postList;
      }else{
        $response['status'] = 0;
      }

      return \Response::json(1);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $response = array(
          'status' => 201,
          'error'   => ''
      );
      $start_time = $this->formatNumber($_POST['start_hour']).':'.$this->formatNumber($_POST['start_minutes']).':'.'00';
      $end_time = $this->formatNumber($_POST['end_hour']).':'.$this->formatNumber($_POST['end_minutes']).':'.'00';

      $post = new Post;
      $nameAddress = $this->getNameProvinceAndDistrict($_POST['province'],$_POST['district']);
      $post->title = $_POST['title'];
      $post->address = $_POST['address'].', '.$nameAddress['district'].', '.$nameAddress['province'].'.';
      $post->email = $_POST['email'];
      $post->website = $_POST['website'];
      $post->phone = $_POST['phone'];
      $post->describe = $_POST['describe'];
      $post->cnt_view = 0;
      $post->cnt_rank = 0;
      $post->latitude = floatval($_POST['latitude']);
      $post->longitude = floatval($_POST['longitude']);
      $post->category_id = intval($_POST['category_id']);
      $post->thumb_id = 46;
      $post->capacity = intval($_POST['capacity']);
      $post->status = 0;
      $post->district = $_POST['district'];
      $post->province = $_POST['province'];
      $post->min_price = intval($_POST['min_price']);
      $post->max_price = intval($_POST['max_price']);
      $post->start_time = date('H:i:s',strtotime($start_time));
      $post->end_time = date('H:i:s',strtotime($end_time));
      $post->avg_food_price =  ( intval($_POST['min_price']) +  intval($_POST['max_price']) ) / 2;
      $post->insert_id = 1;
      $this->setDefaultValue($post,true);
      if(count(Post::where('title','=',$post->title)->get()) > 0){
        $response['status'] = 400;
        $response['error']['title'] = 'Tên địa điểm đã được sử dụng';
      }
      if(count(Post::where('email','=',$post->email)->get()) > 0){
        $response['status'] = 400;
        $response['error']['email'] = 'Email đã được sử dụng';
      }
      if($response['status'] == 201){
        $post->save();
      }

      return \Response::json($response);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $response = array(
          'status'     => 204,
          'data'       => '',
          'statusPost' => false
      );

      $response['data']['detail'] = Post::join('tbl_image','tbl_post.thumb_id','tbl_image.id')
          ->select($this->getColumnPost())
          ->find($id);

      $response['data']['imageList'] = Image::selectRaw('count(category_image) as number_image , category_image , url_image')
          ->where('post_id',$id)
          ->groupby('category_image')->get();

      $response['data']['comment'] = Comment::with(array('User'=>function($query){
        $query->select('id','url_image','fullname');
      },'SubComments.User'=>function($query){
        $query->select('id','url_image','fullname');
      },'Images'=>function($query){
        $query->select('name','comment_id','url_image');
      }))->where('post_id',$id)
          ->select('id','content','user_id','created_at')
          ->get();

      $response['data']['menu'] = Food::where('post_id',$id)->get();

      $response['data']['categoryList'] = DB::select('
        SELECT tbl_category.id , tbl_category.name , IFNULL(tbl_category_temp.quantity_post,0) as quantity_post FROM tbl_category left join
  (
            SELECT count(id) as quantity_post , category_id
            from(
            SELECT
              id,category_id,
              6371 * (
                acos(cos(radians(' . $response['data']['detail']['latitude'] . ')) *
                     cos(radians(latitude)) *
                     cos(radians(longitude) - radians(' . $response['data']['detail']['longitude'] . ')) + sin(radians(' . $response['data']['detail']['latitude'] . ')) *
                                                                     sin(radians(latitude)))) AS distance
            FROM tbl_post
            WHERE deleted_at is null
            HAVING distance <= 2
           ) a
           GROUP BY category_id
          ) as tbl_category_temp on tbl_category.id = tbl_category_temp.category_id
        ');

      $response['data']['locationList'] = DB::select('
            SELECT
             id,title,latitude,longitude,category_id,
              6371 * (
                acos(cos(radians(' . $response['data']['detail']['latitude'] . ')) *
                     cos(radians(latitude)) *
                     cos(radians(longitude) - radians(' . $response['data']['detail']['longitude'] . ')) + sin(radians(' . $response['data']['detail']['latitude'] . ')) *
                                                                     sin(radians(latitude)))) AS distance
            FROM tbl_post
            WHERE deleted_at is null
            HAVING distance <= 2
          ');

      // Check restaurant's status
      if (strtotime($response['data']['detail']['start_time']) <= strtotime("now") && strtotime($response['data']['detail']['end_time']) >= strtotime("now")) {
        $response['statusPost'] = true;
      }

      return \Response::json($response);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function searchByName(){
      // Declare queryString.
      $nowTime = strtotime("now");
      $response = [
          "postList" => "",
          "status"   => 1
      ];

      $postList = DB::table('tbl_post')
          ->select('title','address','start_time','end_time','province','status','tbl_image.url_image')
          ->leftjoin('tbl_image','tbl_post.thumb_id','=','tbl_image.id')
          ->where('province',$_GET['province'])
          ->get();

      foreach($postList as $key => $value){
        if(strtotime($value->start_time) < $nowTime && strtotime($value->end_time) > $nowTime){
          $value->status = 1;
        }
      }

      if(count($postList) > 0){
        $response['postList'] = $postList;
      }else{
        $response['status'] = 0;
      }

      return \Response::json($response);

    }

    public function queryParams($arrayParam,$prefixTable){
      // Declare queryString.
      $queryString = '';

      if(isset($arrayParam['province']) && $arrayParam['province']){
        $queryString .= ' and ' . $prefixTable . '.' . 'province' . ' = ' . $arrayParam['province'];
      }
      
      if(isset($arrayParam['category_id']) && $arrayParam['category_id'] != 0){
        $queryString .= ' and ' . $prefixTable . '.' . 'category_id' . ' = ' . $arrayParam['category_id'];
      }

      if(isset($arrayParam['district']) && $arrayParam['district'] != 0){
        $queryString .= ' and ' . $prefixTable . '.' . 'district' . ' = ' . $arrayParam['district'];
      }

      if(isset($arrayParam['avg_food_price']) && $arrayParam['avg_food_price'] != 0) {
        if ($arrayParam['avg_food_price'] == 1) {
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' >= ' . 0;
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' < ' . 1000000;
        } elseif ($arrayParam['avg_food_price'] == 2) {
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' >= ' . 1000000;
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' < ' . 2000000;
        } elseif ($arrayParam['avg_food_price'] == 3) {
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' >= ' . 2000000;
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' < ' . 4000000;
        } else {
          $queryString .= ' and ' . $prefixTable . '.' . 'avg_food_price' . ' >= ' . 4000000;
        }
      }
      return $queryString;
    }

    public function getNameProvinceAndDistrict($provinceId,$districtId){
      // Get column name of province by id.
      $provinceName = DB::table("province")->select("name")->where("provinceId","=",$provinceId)->first();
      // Get column name of district by id.
      $districtName = DB::table("district")->select("name")->where("districtId","=",$districtId)->first();
      // Storing in $arr.
      $arr = array(
          "province" => $provinceName->name,
          "district" => $districtName->name
      );
      return $arr;
    }

    // Form column to select
    public function getColumnPost(){
      return array('tbl_post.id',
                   'tbl_post.title',
                   'tbl_post.address',
                   'tbl_post.cnt_rank',
                   'tbl_post.latitude',
                   'tbl_post.longitude',
                   'tbl_post.thumb_id',
                   'tbl_post.capacity',
                   'tbl_post.min_price',
                   'tbl_post.max_price',
                   'tbl_post.start_time',
                   'tbl_post.end_time',
                   'tbl_post.thumb_id',
                   'tbl_post.insert_id',
                   'tbl_post.created_at',
                   'tbl_post.avg_food_price',
                   'tbl_post.avg_mark_location',
                   'tbl_post.avg_mark_quality',
                   'tbl_post.avg_mark_serve',
                   'tbl_post.avg_mark_space',
                   'tbl_post.avg_mark_price',
                   'tbl_image.url_image'
          );
    }

}
