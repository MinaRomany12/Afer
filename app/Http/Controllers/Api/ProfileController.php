<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function ChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['success' => false,'message' => 'Current password is incorrect'], 400);
        }
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['success' => true,'message' => 'Password changed successfully'], 200);
    }


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $url = URL.'image/'.uploadimage($request->file('file'), "image", "show");

        return response()->json(['success' => true,'message' => 'uploaded image  successfully','url'=>$url], 201);
    }
    public function UpdataProfile(Request $request)
    {
        $request->validate([
            'profile_image' => 'url',
            'name' => 'string',
            'email' => 'string|email|max:100|unique:users,email,' . auth()->id(),
            'phone' => 'string',
            'front_id' => 'url',
            'back_id' => 'url'
        ]);

        $user = auth()->user();
        if ($request->filled('profile_image')) {
             $user->profile_image = $request->input('profile_image');
        }
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }
        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }
        if ($request->filled('phone')) {
            $user->phone = $request->input('phone');
        }
       if ($request->filled('front_id')) {
        $user->front_id = $request->input('front_id');
        }
        if ($request->filled('back_id')) {
            $user->back_id = $request->input('back_id');
       }
        $user->save();

        return response()->json(['success' => true,'message' => 'Profile uploaded successfully'], 201);
    }

    public function notification(Request $request)
    {
        $validatedData = $request->validate([
            'notification' => 'required|boolean',
        ]);

        $user = auth()->user();
        $user->notification = $validatedData['notification'];
        $user->save();

        // Return a response indicating success, e.g., a JSON response
        return response()->json(['success' => true,'message' => 'Notification setting updated successfully']);
    }

    public function cut ($url)
    {
        $lastSlashPosition = strrpos($url, '/');

        $filename = substr($url, $lastSlashPosition + 1);

         return $filename ;

    }
}
