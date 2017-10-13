<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Image;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
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
      // Upload file to directory.
      if( isset($_FILES) && !empty($_FILES) ){
        // Check directory of post is exist.
        $pathDir = $_GET['path'].$_GET['postid'];
        if (!is_dir($pathDir)) {//hình menu
          mkdir($pathDir);
        }
        // Check file exist or not .
        if( !file_exists($pathDir.'/'.$_FILES['image']['name']) ){
          move_uploaded_file($_FILES['image']['tmp_name'],$pathDir.'/'.$_FILES['image']['name']);
        }
      }else {

        $response = array(
            'status' => 200,
            'data'   => ''
        );
        // Use for update post's mark.
        $counter = 0;
        $mark_location = 0;
        $mark_price    = 0;
        $mark_service  = 0;
        $mark_serve    = 0;
        $mark_space    = 0;
        // Create comment and Image for Comment
        $imageArray = array(
            'name' => '',
            'type' => '',
            'size' => ''
        );

        $comment = new Comment();
        $comment->title = $_POST['title'];
        $comment->content = $_POST['content'];
        $comment->mark_location = $_POST['location'];
        $comment->mark_price = $_POST['price'];
        $comment->mark_service = $_POST['service'];
        $comment->mark_serve = $_POST['serve'];
        $comment->mark_space = $_POST['space'];
        $comment->cnt_mark = ($_POST['location'] + $_POST['service'] + $_POST['serve'] + $_POST['price'] + $_POST['space']) / 5;
        $comment->user_id = $_POST['userId'];
        $comment->post_id = $_POST['postId'];

        if ($comment->save()) {
          // Upload image .
          !empty($_POST['imageName']) ? $imageArray['name'] = explode(',', $_POST['imageName']) : '';
          !empty($_POST['imageType']) ? $imageArray['type'] = explode(',', $_POST['imageType']) : '';
          !empty($_POST['imageSize']) ? $imageArray['size'] = explode(',', $_POST['imageSize']) : '';

          if (!empty($imageArray['name'])) {
            for ($i = 0; $i < count($imageArray['name']); $i++) {
              Image::create([
                  'name'           => $imageArray['name'][$i],
                  'type'           => $imageArray['type'][$i],
                  'size'           => $imageArray['size'][$i],
                  'post_id'        => $_POST['postId'],
                  'category_image' => 4,
                  'comment_id'     => $comment->id,
                  'insert_id'      => $_POST['userId'],
                  'url_image'      => 'public/uploads/photo/' . $_POST['postId'] . '/' . $imageArray['name'][$i]
              ]);
            }
          }
          // Update mark for Post through by Comment
          $commentList = Comment::where('post_id', $_POST['postId'])
              ->select('mark_location', 'mark_price', 'mark_serve', 'mark_service', 'mark_space')
              ->get();

          foreach ($commentList as $key => $value) {
            $mark_location += $value['mark_location'];
            $mark_price += $value['mark_price'];
            $mark_service += $value['mark_service'];
            $mark_serve += $value['mark_serve'];
            $mark_space += $value['mark_space'];
            $counter = $counter + 1;
          }

          $mark_location = $mark_location / $counter;
          $mark_price = $mark_price / $counter;
          $mark_service = $mark_service / $counter;
          $mark_serve = $mark_serve / $counter;
          $mark_space = $mark_space / $counter;

          // Update mark into Post table.
          Post::where('id', $_POST['postId'])->update([
              'avg_mark_location' => round($mark_location, 2),
              'avg_mark_price' => round($mark_price, 2),
              'avg_mark_quality' => round($mark_service, 2),
              'avg_mark_serve' => round($mark_serve, 2),
              'avg_mark_space' => round($mark_space, 2),
              'cnt_rank' => round(($mark_location + $mark_price + $mark_service + $mark_serve + $mark_space) / 5, 1)
          ]);

        }
        return \Response::json($response);
      }
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
