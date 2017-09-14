<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
        //
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
        "imageList" => "",
        "status"   => 1
    ];

    if(isset($postId) && is_numeric($postId)){
      $imageList = DB::table('tbl_image')->select('url_image')->where('post_id',$postId);
      if(isset($typeImage) && is_numeric($typeImage)){
        if($typeImage != 0){
          $imageList = $imageList->where('category_image',$typeImage);
        }
      }
      $imageList = $imageList->get();
      if(count($imageList) > 0){
        $response['imageList'] = $imageList;
      }else{
        $response['status'] = 0;
      }
    }else{
      $response['status'] = 0;
    }

    return \Response::json($response);

  }

}
