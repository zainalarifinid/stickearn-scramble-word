<?php

namespace App\Http\Controllers;
use App\Word;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GameController extends Controller
{
  public function getQuestion(Request $request) {
    $answeredQuestion = json_decode($request->answeredQuestion);
    $question = Word::whereNotIn('id', $answeredQuestion)->inRandomOrder()->first();
    if($question){
      $question->word = str_shuffle($question->word);
      return $question;
    }else {
      return new Response([ 'message' => 'All question has been answered', 'code' => '01' ], 200);
    }
  }

  public function getScore(Request $request, $id) {
    $question = Word::find($id);
    return new Response(['result' => $question->word == $request->answer], 200);
  }

}