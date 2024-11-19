<?php

namespace App\Http\Controllers;

use App\Http\Resources\SinglePostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function addNewPost(Request $request ) {

        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
           

        ]);

        if($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        try {
            $post = new Post();

            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = auth()->user()->id;
            $post->save();

            return response()->json([
                'message' => 'post added successfully',
                
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error'=> $th->getMessage()], 403);
        }

    }

    //edit the post 

    public function editPost(Request $request) {


        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'post_id' => 'required|integer',
           

        ]);

        if($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        try {
         $post_data = Post::find($request->post_id);

         $updatedPost = $post_data->update([
            'title' => $request->title,
            'content' => $request->content,
         ]);

            return response()->json([
                'message' => 'post updated successfully',
                'new_post' => $updatedPost
                
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error'=> $th->getMessage()], 403);
        }

    }


    //retrieve all posts 

    public function getAllPost() {
        try {
            $posts = Post::all();

            return response()->json([
                'post' => $posts
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], 403);
        }
    }

    //fetch single post

    public function getPost($post_id) {

        try {
            
            // $post = Post::find($post_id);

            //or you can use the method below

            // $post = Post::with('user', 'comment', 'likes')->where('id', $post_id)->firstOrFail();

            $post = Post::find($post_id);
            $post_data = new SinglePostResource($post);

            return response()->json([
                'post' => $post_data
            ], 200);


        } catch (\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], 403);
        }
    }

    public function deletePost(Request $request, $post_id) {
        try {
             $post = Post::find($post_id);
             $post->delete();

             return response()->json([
                'message' => 'post deleted successfully'
            ], 200);

        } catch (\Exception $exception) {
            return response()->json(['error'=> $exception->getMessage()], 403);
        }
 
    }

    
    
}
