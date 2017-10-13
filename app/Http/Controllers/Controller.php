<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function formatNumber($number){
    $number = intval($number);
    if($number < 10){
      $number = '0' . $number;
    }
    return $number;
  }

  public function setDefaultValue($object,$isAddNew){
    // If isAddNew parameter is true .
    if($isAddNew) {
//        $object->insert_id = Auth::guard('admin')->user()->id;
      $object->created_at = Carbon::now();
    }
    $object->updated_at = Carbon::now();
  }

  function cvf_convert_object_to_array($data) {

    if (is_object($data)) {
      $data = get_object_vars($data);
    }

    if (is_array($data)) {
      return array_map(array($this,__FUNCTION__), $data);
    }
    else {
      return $data;
    }
  }

}
