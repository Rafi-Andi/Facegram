<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, $username)
    {
        $user = $request->user();

        $following = User::where('username', $username)->first();

        if (!$following) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        if ($following->id === $user->id) {
            return response()->json([
                "message" => "You are not allowed to follow yourself"
            ], 422);
        }

        $followed = Follow::where('following_id', $following->id)->where('follower_id', $user->id)->first();

        if ($followed) {
            return response()->json([
                "message" => "You are already followed",
                "status" => $followed->is_accepted ? "following" : "requested"
            ], 422);
        }
        $follow = Follow::create([
            'follower_id' => $user->id,
            "following_id" => $following->id,
            'is_accepted' => $following->is_private ? 0 : 1
        ]);

        return response()->json([
            "message" => "Follow success",
            "status" => $follow->is_accepted ? "following" : "requested"
        ], 200);
    }

    public function unfollow(Request $request, $username)
    {
        $user = $request->user();

        $following = User::where('username', $username)->first();
        if (!$following) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $followed = Follow::where('following_id', $following->id)->where('follower_id', $user->id)->first();

        if (!$followed) {
            return response()->json([
                "message" =>  "You are not following the user",
            ], 422);
        }

        $followed->delete();
        return response()->json([
            "message" => "Success unfollow",
            "status" => "not-following"
        ], 200);
    }

    public function following(Request $request, $username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $following = Follow::with('following')->where('follower_id', $user->id)->get();

        return response()->json([
            "following" => UserResource::collection($following)
        ], 200);
    }

    public function accept(Request $request, $username)
    {
        $user = $request->user();

        $userFollower = User::where('username', $username)->first();

        if (!$userFollower) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $followed = Follow::where('following_id', $user->id)->where('follower_id', $userFollower->id)->first();
        if (!$followed) {
            return response()->json([
                "message" => "The user is not following you"
            ], 422);
        }
        if ($followed->is_accepted) {
            return response()->json([
                "message" => "Follow request is already accepted"
            ], 422);
        }

        $followed->update([
            "is_accepted" => true
        ]);

        return response()->json([
            "data" => $followed
        ], 200);
    }

    public function followers(Request $request, $username){
       $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        $follower = Follow::with('follower')->where('following_id', $user->id)->get();
        
        return response()->json([
            "followers" => FollowerResource::collection($follower)
        ], 200); 
    }
}
