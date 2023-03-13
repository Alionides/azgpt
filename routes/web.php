<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;
use Stichoza\GoogleTranslate\GoogleTranslate;

use Illuminate\Support\Facades\RateLimiter;

Route::middleware(['throttle:global'])->group(function () {

    Route::get('/', function (Request $request) {

        $ip = $_SERVER['REMOTE_ADDR'];
        getUniqueVisitorCount($ip);
        $q_count = getUniqueQuestionCount('');
        $i_count = getUniqueImageCount('');
        $messages = collect(session('messages', []))->reject(fn($message) => $message['role'] === 'system');
        return view('welcome', [
            'messages' => $messages,
            'visitors' => $_SESSION['visitor_count'],
            'questions' => $q_count,
            'images' => $i_count,
        ]);
    });

    Route::post('/image', function (Request $request) {
        $request->session()->forget('messages');
        $az_text = $request->input('message');
        $tr = new GoogleTranslate();
        $tr->setSource('az');
        $tr->setTarget('en');
        $en_text = $tr->translate($az_text);
        $new_messages[] = ['role' => 'user', 'content' => $az_text];

        $response = OpenAI::images()->create([
            'prompt' => $en_text,
            'n' => 1,
            'size' => '512x512',
            'response_format' => 'url',
        ]);

        $image = [];
        foreach ($response->data as $data) {
           $image['image'] = $data->url;
            $data->b64_json;
        }
        $new_messages[] = ['role' => 'assistant', 'content' => $image];
        //return $new_messages;
        $request->session()->put('messages', $new_messages);
        getUniqueImageCount($az_text);
        return redirect('/');

    });
    Route::post('/', function (Request $request) {
        $request->session()->forget('messages');
        $messages = $request->session()->get('messages', [
            ['role' => 'system', 'content' => 'You are AZGPT - A ChatGPT clone. Answer as concisely as possible.']
        ]);

        $az_text = $request->input('message');
        $tr = new GoogleTranslate();
        $tr->setSource('az');
        $tr->setTarget('en');
        $en_text = $tr->translate($az_text);

        $messages[] = ['role' => 'user', 'content' => $en_text];
        $new_messages[] = ['role' => 'user', 'content' => $az_text];

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
        ]);

        $fin = new GoogleTranslate();
        $fin->setSource('en');
        $fin->setTarget('az');
        $translated = $fin->translate($response->choices[0]->message->content);

        $new_messages[] = ['role' => 'assistant', 'content' => $translated];

        $request->session()->put('messages', $new_messages);
        getUniqueQuestionCount($az_text);
        return redirect('/');
    });

    Route::get('/reset', function (Request $request) {
        $request->session()->forget('messages');
        return redirect('/');
    });
});


function getUniqueVisitorCount($ip)
{
    session_start();
    if(!isset($_SESSION['current_user']))
    {
        $file = 'counter.txt';
        if(!$data = @file_get_contents($file))
        {
            file_put_contents($file, base64_encode($ip));
            $_SESSION['visitor_count'] = 1;
        }
        else{
            $decodedData = base64_decode($data);
            $ipList = explode(';', $decodedData);

            if(!in_array($ip, $ipList)){
                array_push($ipList, $ip);
                file_put_contents($file, base64_encode(implode(';', $ipList)));
            }
            $_SESSION['visitor_count'] = count($ipList);
        }
        $_SESSION['current_user'] = $ip;
    }
}
function getUniqueQuestionCount($question) : int
{
        $file = 'questions.txt';
        if(!$data = @file_get_contents($file))
        {
            file_put_contents($file, base64_encode($question));
            $question_count = 1;
        }
        else{
            $decodedData = base64_decode($data);
            $questionList = explode(';', $decodedData);

            if(!in_array($question, $questionList)){
                array_push($questionList, $question);
                file_put_contents($file, base64_encode(implode(';', $questionList)));
            }
            $question_count = count($questionList);
        }
        return $question_count;
}
function getUniqueImageCount($image) : int
{
        $file = 'images.txt';
        if(!$data = @file_get_contents($file))
        {
            file_put_contents($file, base64_encode($image));
            $image_count = 1;
        }
        else{
            $decodedData = base64_decode($data);
            $imageList = explode(';', $decodedData);

            if(!in_array($image, $imageList)){
                array_push($imageList, $image);
                file_put_contents($file, base64_encode(implode(';', $imageList)));
            }
            $image_count = count($imageList);
        }
        return $image_count;
}
