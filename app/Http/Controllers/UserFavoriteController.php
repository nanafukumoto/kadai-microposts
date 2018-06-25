<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFavoriteController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::user()->fav($id);
        return redirect()->back();
    }
    
    public function destroy($id)
    {
        \Auth::user()->unfav($id);
        return redirect()->back();
    }
    

    public function favorite($userid) {
        $user = \App\User::find($userid);
        $posts = $user -> favorite() ->paginate(10);
        $data = [
            'user' => $user,
            'microposts' => $posts,
            ];
        $data += $this-> counts($user);
        return view('users.favoritings',$data);
    }

}
