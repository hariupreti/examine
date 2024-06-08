<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Result;
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

    public function submitAnswer(Request $request){
        $questions = Question::with('answers')->get();
        $questionResults = [];

        foreach ($questions as $question) {
            $result = [
                'user_id' => session("id"),
                'question_id' => $question->id,
                'answer_id' => null,
                'user_action' => '',
                'is_correct' => false
            ];

            foreach ($question->answers as $answer) {
                if (isset($selectedAnswers['question_' . $question->id])) {
                    $selectedAnswerId = $selectedAnswers['question_' . $question->id];
                    if ($answer->id == $selectedAnswerId) {
                        $result['answer_id'] = $answer->id;
                        $result['selected_answer_text'] = $answer->answer_text;

                        if ($answer->is_correct) {
                            $result['user_action'] = 'right';
                            $result['is_correct'] = true;
                        } else {
                            $result['user_action'] = 'wrong';
                            $result['is_correct'] = false;
                        }
                    }
                }
                if ($answer->is_correct) {
                    $result['answer_id'] = $answer->id;
                }
            }

            if (empty($result['selected_answer_id'])) {
                $result['user_action'] = 'skip';
            }
            $questionResults[] = $result;
            Result::insert($questionResults);
        }
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
