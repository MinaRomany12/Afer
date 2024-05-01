<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detail;
use App\Models\InfoWork;

use App\Models\User;
use Illuminate\Http\Request;


class DetailController extends Controller
{
    public function details(Request $request)
    {
        $validatedData = $request->validate([
            'info' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $detail = new Detail();
        $detail->info = $validatedData['info'];
        if ($request->hasFile('image'))
        {
            $detail->image =URL."details_user/". uploadimage($request->image,"details_user","show");
        }

        $detail->user_id = auth()->user()->id;
        $detail->save();

        return response()->json(['success'=>true,'message' => 'Detail created successfully'], 201);
    }
    public function info_work(Request $request)
    {
        $validatedData = $request->validate([
            'time' => 'string',
            'location' => 'required|url',
        ]);

        $info_work = new InfoWork();
        $info_work->time = $validatedData['time'];
        $info_work->location = $validatedData['location'];
        $info_work->user_id = auth()->user()->id;
        $info_work->save();

        return response()->json(['success'=>true,'message' => 'add Information work successfully'], 201);
    }

   public function showUser(Request $request)
    {
    $validatedData = $request->validate([
        'id' => 'required|string',
    ]);

    $user = User::find($validatedData['id']);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    if ($user->job == 'patient') {
        $details = $user->details()->get();
        return response()->json(['success' => true, 'data' => $user, 'details' => $details]);
    }

    if ($user->job == 'doctor') {
        $details = $user->details()->get();
        $info_work = $user->info_work()->get();
        return response()->json(['success' => true, 'data' => $user, 'info_work' => $info_work, 'details' => $details]);
    }
   }



}
