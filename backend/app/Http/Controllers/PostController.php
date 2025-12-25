<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostAttachment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $size = max(1, (int) $request->get('size', 10)); $page = max(0, (int) $request->get('page', 0));
        $validator = Validator::make($request->all(), [
            "page" => "min:0",
            "size" => "min:0|numeric"
        ]);

        $validated = $validator->validate();

        if ($validator->fails()) {
            return response()->json([
                "message" => "Invalid Field",
                "errors" => $validator->errors()
            ], 422);
        }
        $posts = Post::with('user', 'attachments')->orderBy('created_at', 'desc')->skip($page * $size)->take($size)->get();
        return response()->json(
            [
                "page" => $page,
                "size" => $size,
                "posts" => $posts
            ],
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "caption" => "required|string",
            "attachments" => "required|array|min:1",
            "attachments.*" => "required|file|image|mimes:jpg,jpeg,webp,png,gif"
        ]);

        $validated = $validator->validate();

        if ($validator->fails()) {
            return response()->json([
                "message" => "Invalid Field",
                "errors" => $validator->errors()
            ], 422);
        }


        try {
            DB::beginTransaction();
            $user = $request->user();

            $post = $user->posts()->create([
                "caption" => $validated['caption']
            ]);

            foreach ($validated['attachments'] as $file) {

                $attPath = 'posts/' . $file->getClientOriginalName();
                PostAttachment::create([
                    "storage_path" => $attPath,
                    "post_id" => $post->id
                ]);
            }
            DB::commit();

            return response()->json([
                "message" => "Create post success",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();

        $post = Post::where('id', $id)->first();

        if (!$post) {
            return response()->json([
                "message" => "Post not found"
            ], 404);
        }
        if ($user->id !== $post->user_id) {
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        $post->delete();

        return response()->json(204);
    }
}
