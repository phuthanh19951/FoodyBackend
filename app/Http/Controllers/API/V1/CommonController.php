<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Response;

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
        select count(tbl_post.province) as numberpost , province.name , province.provinceid
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

}
