<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Image;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
      echo 2;
      print_r($_FILES);
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
          'status' => 400,
          'data'   => '',
          'error'  => ''
      );

      // Check directory of post is exist.
      $pathDir = $_POST['path'].$_POST['postId'];
      if (!is_dir($pathDir)) {//hình menu
        mkdir($pathDir);
      }

      if(isset($_FILES['images']) && !empty($_FILES['images'])){
        if(isset($_POST['postId']) && is_numeric($_POST['postId'])){
          for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $image = Image::create([
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'size' => $_FILES['images']['size'][$i],
                'post_id' => $_POST['postId'],
                'category_image' => $_POST['typeImages'][$i],
                'comment_id' => 0,
                'insert_id' => isset($_POST['userId']) ? $_POST['userId'] : 1,
                'url_image' => 'public/uploads/photo/' . $_POST['postId'] . '/' . $_FILES['images']['name'][$i]
            ]);
            // Check file exist or not .
            if( !file_exists($pathDir.'/'.$_FILES['images']['name'][$i]) ){
              move_uploaded_file($_FILES['images']['tmp_name'][$i],$pathDir.'/'.$_FILES['images']['name'][$i]);
            }
            if(count($image) > 0){
              $response['status'] = 200;
            }
          }
        }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

  public function getImages($postId,$typeImage = 0){
    $response = [
        "data" => array(
            "imageList" => array()
        ),
        "status"   => 204
    ];

    if(isset($postId) && is_numeric($postId)){
      $imageList = DB::table('tbl_image')->select('url_image')->where('post_id',$postId);
      if(isset($typeImage) && is_numeric($typeImage)){
        if($typeImage != 0){
          $imageList = $imageList->where('category_image',$typeImage);
          // Get image from tbl_image_comment table.
        }
      }
      $imageList = $imageList->get();
      if( count($imageList) > 0 ){
        $response['data']['imageList'] = $imageList;
        $response['status'] = 200;
      }
    }

    return \Response::json($response);
  }

}
