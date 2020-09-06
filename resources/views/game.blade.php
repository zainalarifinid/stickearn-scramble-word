<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 4em;
            }

            .title > span {
                display: block;
                font-size: 20px;
            }

            #container-scramble {
                display: none;
            }

            .dashboard-game {
                font-size: 2em;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
            }

            .question {
                font-size: 6em;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .input-answer {
                padding: 10px;
                text-align: center;
                font-size: 3em;
                margin-bottom: 10px;
                text-transform: uppercase;
            }

            #log_game {
                height: 100px;
                max-height: 100px;
                max-width: 520px;
                margin: 0px auto;
                overflow-y: auto;
            }

            .animation-score{
                display: none;
            }

            .animation-guessed {
                display: none;
            }

            .correct-score {
                color: #00ff00;
            }

            .wrong-score {
                color: #ff0000;
            }

        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Scramble Word Stickearn
                    <span>by : <a href="mailto:arifin.1602@gmail.com">Zainal Arifin</a></span>
                </div>
                <div id="container-scramble" >
                    <div class="dashboard-game" >
                        <div>
                            Score : <span id="score" >100</span><b class="animation-score correct-score" >+100</b> 
                        </div>
                        <div>
                            Guessed Word : <span id="word_count" >10</span><b class="animation-guessed correct-score" >+1</b>
                        </div>
                    </div>
                    <h2 class="question" >loading...</h2>
                </div>
                <form name="form-answer" >
                    <input class="form-control input-answer" id="user_answer" autofocus />
                    <div id="log_game" >Please type <i>START</i> to start the game.</div>
                </form>
            </div>
        </div>
    </body>
    <script>
        var urlAPI = '/api';
        var formInput = document.getElementsByName('form-answer')[0];
        var displayQuestion = document.getElementsByClassName('question')[0];
        var displayScore = document.getElementById('score');
        var displayGuessedWord = document.getElementById('word_count');
        var logGame = document.getElementById('log_game');
        var score = document.getElementsByClassName('animation-score')[0];
        var guessed = document.getElementsByClassName('animation-guessed')[0];

        var currentScore = 0;
        var currentGuessedWord = 0;
        var currentQuestion = 0;
        var isGameStarted = false;
        var answeredQuestion = [];
        
        function fadeout(element){
            var op = 1;  // initial opacity
            var timer = setInterval(function () {
                if (op <= 0.1){
                    clearInterval(timer);
                    element.style.display = 'none';
                }
                element.style.opacity = op;
                element.style.filter = 'alpha(opacity=' + op * 100 + ")";
                op -= op * 0.1;
            }, 50);
        }
        function setQuestion() {
            var http = new XMLHttpRequest();
            http.open('POST', `${urlAPI}/word`);
            http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            http.send(`answeredQuestion=[${answeredQuestion.toString()}]`);
            http.addEventListener('load', function(){
                var question = JSON.parse(http.responseText);
                if(question.hasOwnProperty('code') && question.code == '01') {
                    displayQuestion.innerText = 'Victory!';
                    logGame.innerHTML = 'Congratulation! ' + question.message + '<br/>' + logGame.innerHTML;

                } else {
                    displayQuestion.innerText = question.word;
                    currentQuestion = question.id;
                }
            });
        }

        function sendAnswer(id, answerValue) {
            var http = new XMLHttpRequest();
            http.open('POST', `${urlAPI}/word/`+id);
            http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            http.send(`answer=${answerValue}`)
            http.addEventListener('load', function(){
                var feedback = JSON.parse(http.responseText);
                if(feedback.result){
                    currentScore += 100;
                    currentGuessedWord += 1;
                    var newMessage = 'Correct answer <b class="correct-score" >+100</b> for <b>'+answerValue+'</b><br/>';
                    logGame.innerHTML = newMessage + logGame.innerHTML;
                    score.classList.remove('wrong-score');
                    score.classList.add('correct-score');
                    score.innerText = '+100';
                    score.style.display = 'inline-block';
                    guessed.style.display = 'inline-block';
                    fadeout(score);
                    fadeout(guessed);
                    displayScore.innerText = currentScore;
                    displayGuessedWord.innerText = currentGuessedWord;
                    answeredQuestion.push(id);
                    displayQuestion.innerText = 'loading...';
                    setQuestion();
                }else{
                    currentScore -= 10;
                    var newMessage = 'Bad answer <b class="wrong-score" >-10</b> for <b>'+answerValue+'</b><br/>';
                    logGame.innerHTML = newMessage + logGame.innerHTML;
                    score.classList.add('wrong-score');
                    score.classList.remove('correct-score');
                    score.innerText = '-10';
                    score.style.display = 'inline-block';
                    fadeout(score);
                    displayScore.innerText = currentScore;
                    displayGuessedWord.innerText = currentGuessedWord;
                }
            });
        }
        
        formInput.onsubmit = function(e) {
            e.preventDefault();
            var answer = document.getElementById('user_answer');
            var answerValue = answer.value;
            answer.value = '';
            if(answerValue === 'start' && !isGameStarted) {
                isGameStarted = true;
                var formInput = document.getElementById('container-scramble');
                logGame.innerHTML = 'Game has been started!<br/> Have Fun!';
                formInput.style.display = 'block';
                displayScore.innerText = 0;
                displayGuessedWord.innerText = 0;
                setQuestion();
            }else{
                sendAnswer(currentQuestion, answerValue);
            }
        };
    </script>
</html>
