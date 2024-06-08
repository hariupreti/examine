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

    private function getCorrectAnswers()
    {
        $questions = Question::with('answers')
            ->get();
        $correctAnswers = [];
        foreach ($questions as $question) {
            foreach ($question->answers as $answer) {
                if ($answer->is_correct) {
                    $correctAnswers['question_' . $question->id] = $answer->id;
                    break;
                }
            }
        }
        return $correctAnswers;
    }

    public function submitAnswer(Request $request){
        $selectedAnswers = $request->all();
        $correctAnswers = self::getCorrectAnswers();

        $resultsToInsert = [];
        foreach ($selectedAnswers as $questionKey => $answerId) {
            $questionId = intval(str_replace('question_', '', $questionKey));
            $question = Question::find($questionId);
            if ($question) {
                $isCorrect = ($answerId == $correctAnswers[$questionKey]);
                $userAction = $isCorrect ? 'right' : 'wrong';
                $resultsToInsert[] = [
                    'user_id' => session("id"),
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'is_correct' => $isCorrect,
                    'user_action' => $userAction,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        Result::insert($resultsToInsert);
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
