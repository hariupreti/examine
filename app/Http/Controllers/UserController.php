<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showForm()
    {
        $userData = [
            "id" => "",
            "username" =>  ""
        ];
        session($userData);
        return view('start',['user_id' => session('id')]);
    }

    public function processForm(Request $request){
        $userData = [
            "id" => self::generateUniqueId(10),
            "username" =>  $request->name
        ];
        session($userData);
        $questionWithAnswers = self::getRandomQuestion();
        return response()->json(["qa" => $questionWithAnswers],200);
    }

    public function questionList(){
        dd(session("id"));
    }

    private function generateUniqueId($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    private function getRandomQuestion(){
    return DB::select("
            SELECT
            q.id AS question_id,
            q.question_text,
            GROUP_CONCAT(CONCAT(a.id, ': ', a.answer_text) ORDER BY RAND()) AS answers
            FROM
            questions q
            JOIN
            answers a ON q.id = a.question_id
            GROUP BY
            q.id, q.question_text
            ORDER BY
            RAND()
            LIMIT 5
        ");
    }

}
