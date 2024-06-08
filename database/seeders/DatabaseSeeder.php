<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Answer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ///let's create a QuestionAnswerSeeder

        $questions = [
            [
                'question_text' => 'What does PHP stand for?',
                'answers' => [
                    ['answer_text' => 'Personal Home Page', 'is_correct' => false],
                    ['answer_text' => 'Hypertext Preprocessor', 'is_correct' => true],
                    ['answer_text' => 'Private Home Page', 'is_correct' => false],
                    ['answer_text' => 'Programming Home Page', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'Which version of PHP introduced the try/catch exception handling?',
                'answers' => [
                    ['answer_text' => 'PHP 4', 'is_correct' => false],
                    ['answer_text' => 'PHP 5', 'is_correct' => true],
                    ['answer_text' => 'PHP 5.3', 'is_correct' => false],
                    ['answer_text' => 'PHP 7', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'Which of the following is used to declare a constant in PHP?',
                'answers' => [
                    ['answer_text' => 'define()', 'is_correct' => true],
                    ['answer_text' => 'constant()', 'is_correct' => false],
                    ['answer_text' => 'const()', 'is_correct' => false],
                    ['answer_text' => 'declare()', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'Which function is used to get the length of a string in PHP?',
                'answers' => [
                    ['answer_text' => 'strlen()', 'is_correct' => true],
                    ['answer_text' => 'length()', 'is_correct' => false],
                    ['answer_text' => 'strlength()', 'is_correct' => false],
                    ['answer_text' => 'strlenf()', 'is_correct' => false],
                ]
            ],
            [
                'question_text' => 'Which of the following is a PHP array function?',
                'answers' => [
                    ['answer_text' => 'array_push()', 'is_correct' => true],
                    ['answer_text' => 'array_length()', 'is_correct' => false],
                    ['answer_text' => 'array_size()', 'is_correct' => false],
                    ['answer_text' => 'array_reverse()', 'is_correct' => true],
                ]
            ]
        ];

        foreach ($questions as $questionData) {
            $question = Question::create(['question_text' => $questionData['question_text']]);

            foreach ($questionData['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct']
                ]);
            }
        }
    }
}
