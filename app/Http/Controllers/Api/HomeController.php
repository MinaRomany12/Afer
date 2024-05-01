<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function search(Request $request)
        {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $user_id = auth()->user()->id;

            $users = User::where('name', 'like', "%$key%")
                ->where('id', '!=', $user_id)
                ->select('*')
                ->get();

            if ($users->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No users found.']);
            }

            return response()->json(['success' => true, 'message' => 'Found the user', 'user' => $users]);
        }

    public function ShowMore(Request $request) {
        $request->validate([
            'job' => 'required|string|in:patient,doctor',
        ]);


        $job = $request->input('job');
        $user_id = auth()->user()->id;
        $users = User::where('job', 'like', $job)
            ->where('id', '!=', $user_id)
            ->select('*')
            ->get();

        return response()->json(['success' => true, 'message' => "users is $job",'users' => $users]);
 }

    public function home()
    {
        $user_id = auth()->user()->id;
        $users_doctor = User::where('job',  'like','doctor')->where('id', '!=', $user_id)->select( '*')->take(6)->get();

        foreach ($users_doctor as $user) {
            unset($user);
        }
        $users_patient = User::where('job', 'like', 'patient')->where('id', '!=', $user_id)->select( '*')->take(6)->get();

        foreach ($users_patient as $user) {
            unset($user);
        }

        return response()->json(['success' => true,"message"=>"home users",'users_doctor'=>$users_doctor,'users_patient'=>$users_patient ]);
    }
}
