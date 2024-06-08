<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Examine</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
        .form-horizontal{
        position: absolute;
        left: 0;
        right: 0;
        top: 10%;
        margin: 0 auto;  
        width: 50%;
        }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>
    </head>
    <body class="container p-20 mx-auto">
        {{-- user form --}}
        <form style="<?php echo !empty($user_id) ? "display:none;":"display:block;" ?>" id="userSessionForm" class="form-horizontal" action={{ route('username.process') }}>
            <div class="mb-3">
              <label for="username" class="form-label">Name</label>
              <input type="text" name="username" class="form-control" id="username" aria-describedby="username">
            </div>
            <button type="submit" class="btn btn-primary">Start Test</button>
        </form>

        <div class="container mt-5" >
            <form class="form-horizontal" action={{ route('answer.process') }} id="questions-container">

            </form>
        </div>

        {{-- showing results --}}

        <div class="container" id="result">
            <div class="card">
                <div class="card-body">
                    <h4>Quiz Results</h4>
                    <table id="quiz-results-table" class="table">
                        <thead>
                            <tr>
                                <th>Total Questions</th>
                                <th>Right Answers</th>
                                <th>Wrong Answers</th>
                                <th>Skip Answers</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                
        <script>
            $('#result').hide();
            $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });

            $(document).ready(function() {
            $('#userSessionForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(), 
                success: function(response) {
                    if (response.qa.length > 0) {
                    $('#userSessionForm').hide();
                    var html = '';
                    response.qa.forEach(function(question) {
                        html += '<div class="question">';
                        html += '<h3>' + question.question_text + '</h3>';
                        html += '<ul>';
                        let ans = question.answers.split(",");
                        ans.forEach(function(answer) {
                            var parts = answer.split(':');
                            var answerId = parts[0].trim();
                            var answerText = parts[1].trim();
                            html += '<label>';
                            html += '<input type="radio"  name="question_' + question.question_id + '" value="' + answerId + '">';
                            html += answerText;
                            html += '</label><br>';
                        });
                        html += '</ul>';
                        html += '</div>';
                    });
                    html += '<br><br><button type="submit" class="btn btn-primary">Submit Answer</button>'; 
                    $('#questions-container').html(html); // Append HTML to container
                    } else {
                        $('#questions-container').html('<p>No questions found.</p>');
                    }   
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
            });
            });

            // Handle question submit through ajax
            $(document).ready(function() {
            $('#questions-container').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(), 
                success: function(res) {
                var resultsTable = $('#result').find('tbody');
                resultsTable.empty();
                var newRow = '<tr>' +
                    '<td>' + res.totalQuestions + '</td>' +
                    '<td>' + res.rightCount + '</td>' +
                    '<td>' + res.wrongCount + '</td>' +
                    '<td>' + res.skipCount + '</td>' +
                    '<td>' + res.score + ' / ' + res.totalQuestions + '</td>' +
                    '</tr>';
                resultsTable.append(newRow);
                $('#questions-container').hide();
                $('#result').show();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
            });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>