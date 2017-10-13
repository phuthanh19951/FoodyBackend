<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubComment;
use Illuminate\Support\Facades\DB;

class SubCommentController extends Controller
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
        $response = array(
            'status' => 201,
            'data'   => array()
        );

        $subComment = new SubComment();

        $subComment->content    = $_POST['contentComment'];
        $subComment->comment_id = $_POST['commentId'];
        $subComment->user_id    = $_POST['userId'];

        if($subComment->save()){
          $response['status'] = 200;
          $response['data'] = DB::table('tbl_sub_comment')
              ->select('tbl_sub_comment.content','tbl_users.fullname','tbl_users.url_image','tbl_sub_comment.created_at')
              ->join('tbl_users','tbl_sub_comment.user_id','tbl_users.id')
              ->where('tbl_sub_comment.id',$subComment->id)
              ->get();
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
}
