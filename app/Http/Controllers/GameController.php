<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 0);
        $size = $request->input('size', 10);

        $games = Game::with('user')->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'page' => $page,
            'size' => $size,
            'totalElements' => $games->count(),
            'content' => $games->map(function ($game) {
                return [
                    "slug" => $game->slug,
                    "title" => $game->title,
                     "description" => $game->description,
                    "author" => $game->user->username,

                ];
            })
        ], 200);
    }

    public function store(Request $request)
    {
        $params = $request->validate([
           'title' => ['required', 'min:3' , 'max:60'],
            'description' => ['required', 'max:200']
        ]);

        $params['slug'] = Str::slug($params['title'], '-');

        if(Game::where('slug', $params['slug'])->exists()) {
            return response()->json([
                "status"=> "invalid",
                "slug"=>"Game title already exists"
            ],400);
        }

        $request->user()->games()->create($params);

        return response()->json([
            "status"=> "success",
            'slug' => $params['slug']
        ],201);
    }
}
