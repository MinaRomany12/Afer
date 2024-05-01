<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionEn;
use Illuminate\Http\Request;

class QusetionController extends Controller
{
    public function beck(Request $request )
    {

        $language = $request->header('Accept-Language');

        // Assuming you have different questions for each language
        if ($language === 'ar') {
            $questions = Question::all();
        } else {
            $questions =QuestionEn::all();
        }

        return response()->json($questions);

    }
}
