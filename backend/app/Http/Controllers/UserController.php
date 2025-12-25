<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllUserResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $userLogged = $request->user();

        $users = User::with(['following' => function ($q) use ($userLogged) {
            $q->where('following_id', $userLogged->id);
        }])->where('id', '!=', $userLogged->id)->get();

        $usersNotFollowed = [];

        foreach ($users as $user) {
            $isFollowed = false;
            foreach ($user->following as $following) {
                if ($following->following_id === $userLogged->id) {
                    $isFollowed = true;
                }
            }

            if (!$isFollowed) {
                $usersNotFollowed[] = $user;
            }
        }

        return response()->json([
            "users" => AllUserResource::collection($usersNotFollowed),
        ], 200);
    }

    public function show(Request $request, $username)
    {

        $userLogged = $request->user();

        $user = User::with(['follower' => function ($q) {
            $q->where('is_accepted', 1);
        }, 'following' => function ($q) {
            $q->where('is_accepted', 1);
        }, 'posts' => function ($q) {
            $q->with('attachments')->latest();
        }])->where('username', $username)->first();
        $userLoggedFollowing = $userLogged->following()->where('following_id', $user->id)->first();

        $followingStatus = '';
        if (!$userLoggedFollowing) {
            $followingStatus = 'not-following';
        } elseif (!$userLoggedFollowing->is_accepted) {
            $followingStatus = 'requested';
        } else {
            $followingStatus = 'following';
        }

        return response()->json([
            "id" => $user->id,
            "full_name" => $user->full_name,
            "username" => $user->username,
            "bio" => $user->bio,
            "is_private" => $user->is_private,
            "created_at" => $user->created_at,
            "is_your_account" => $user->id === $userLogged->id ? true : false,
            "following_status" => $followingStatus,
            "posts_count" => $user->posts->count(),
            "followers_count" => $user->follower->count(),
            "following_count" => $user->following->count(),
            "posts" => $user->posts
        ], 200);
    }
}
