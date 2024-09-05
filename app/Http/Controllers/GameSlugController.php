<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;

class GameSlugController extends Controller
{
    public function uploadFile($slug, Request $request)
    {
       $token =$request->input('token');
        $userId = $this->validateToken($token);
       if(!$userId){
            return response('Invalid Session Token', 403);
       }

       if(!$request->hasFile('zipfile') || !$request->hasFile('thumbnail')){
           return response('No File Uploaded', 400);
       }

       $game = Game::where('slug', $slug)->first();
           if(!$game) {
               return response('Game Not Found', 404);
           }

        if($game->created_by != $userId){
            return response('User is not the game Author', 403);
        }


        $zipfile = $request->file('zipfile');
        $thumbnail = $request->file('thumbnail');
        $zipPath = $zipfile->store('game_files');
        $thumbnailPath = $thumbnail->store('thumbnail_files');

        $game->file_path = $zipPath;
        $game->thumbnail_path = $thumbnailPath;
        $game->save();

        return response('File and thumbnail uploaded successfully.', 201);


    }

    private function validateToken($token)
    {
        $personalToken = PersonalAccessToken::where('token', $token)->first();
        if ($personalToken) {
            return $personalToken->user_id;
        }
        return false;
    }



}
