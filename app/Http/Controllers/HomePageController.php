<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Vocab;

class HomePageController extends Controller
{
    public function show():View {
        $my_vocabs = [];
        $vocabs = Vocab::all();
        foreach($vocabs as $vocab){
            $my_vocabs[$vocab->word] = $vocab->meaning;
        }
        return view('homepage', ['results' => [], 'vocabs' => $my_vocabs]);
    }

    public function generate_meanings(Request $request):View{
        set_time_limit(60);
        $words = $request->json('words');
        $results = [];
        $words_dict = [];
        foreach($words as $word){
            $entry = null;
            $meanings = [];
            if(!array_key_exists($word, $words_dict)){
                $response = Http::get('https://jisho.org/api/v1/search/words', [
                    'keyword' => $word
                ]);
    
                if($response->successful()){
                    $result = $response->json();
    
                    if(isset($result['data']) && is_array($result['data']) && count($result['data']) > 0){
                        foreach($result['data'] as $data){
                            if($data['slug'] == $word){
                                $entry = $data;
                                $words_dict[$word] = "RESPONSE SUCCESSFULL";
                                break;
                            }
                        }
                        if($entry == null){
                            $entry = $result['data'][0]; // if not matched, get the first entry
                        }
    
                        if(isset($entry['senses']) && is_array($entry['senses'])){
                            foreach($entry['senses'] as $sense){
                                if(isset($sense['english_definitions']) && is_array($sense['english_definitions'])){
                                    $meanings[] = implode(', ', $sense['english_definitions']); 
                                }
                            }
                        }
                        
                        $results[$word] = implode('; ', $meanings);
                        
                    }
                } else{
                    $words_dict[$word] = "RESPONSE UNSUCCESSFULL";
                }
            }
        }

        $my_vocabs = [];
        $vocabs = Vocab::all();
        foreach($vocabs as $vocab){
            $my_vocabs[$vocab->word] = $vocab->meaning;
        }

        return view('homepage', ['results' => $results, 'vocabs' => $my_vocabs]);
    }

    public function store_vocab(Request $request):View{
        $vocab = $request->json('vocab');
        $results = $request->json('results');
        $word = $vocab['word'];
        $meaning = $vocab['meaning'];

        try{
            $vocab = Vocab::create([
                'word' => $word,
                'meaning' => $meaning
            ]);    
        } catch(\Exception $e){
            Log::error('Error adding: ' . $e->getMessage());
            // Flash an error message to the session
            return redirect()->back()->with('error_add', 'Failed to add. Please try again.');
        }

        $my_vocabs = [];
        $vocabs = Vocab::all();
        foreach($vocabs as $vocab){
            $my_vocabs[$vocab->word] = $vocab->meaning;
        }

        return view('homepage', ['results' => $results, 'vocabs' => $my_vocabs]);
    }

    public function delete_vocab(Request $request):View{
        $word = $request->json('word');
        $results = $request->json('results');
        try{
            $vocab = Vocab::where('word', $word)->first();

            if ($vocab) {
                $vocab->delete();
            }
        } catch(\Exception $e){
            Log::error('Error deleting: ' . $e->getMessage());
            // Flash an error message to the session
            return redirect()->back()->with('error_delete', 'Failed to delete. Please try again.');
        }

        $my_vocabs = [];
        $vocabs = Vocab::all();
        foreach($vocabs as $vocab){
            $my_vocabs[$vocab->word] = $vocab->meaning;
        }

        return view('homepage', ['results' => $results, 'vocabs' => $my_vocabs]);
    }

    public function generate_question(){
        $vocabs = Vocab::all();
        if(count($vocabs) == 0){
            $message = "No vocabs found !";
        } elseif(count($vocabs) < 3){
            $message = "Not enough vocabs to generate a question!";
        }
        else{
            $message = null;
            $vocab = $vocabs->random();
            $word = $vocab->word;
            $correctMeaning = $vocab->meaning;
            $meanings = $vocabs->pluck('meaning')->unique();;
            $meanings = $meanings->filter(fn($meaning) => $meaning !== $correctMeaning);
            $options = $meanings->random(3)->toArray();
            array_push($options, $correctMeaning);
            shuffle($options);

        }
        return view('homepage', ['results' => [], 'vocabs' => [], 'message' => $message, 'word' => $word ?? null, 'options' => $options ?? null]);
    }

    public function check_answer(Request $request):View{
        $word = $request->json('word');
        $meaning = $request->json('meaning');
        $word = Vocab::where('word', $word)->first();
        if($word->meaning == $meaning){
            $test_result = "Correct Answer";
        } else{
            $test_result = "Incorrect Answer";
        }

        return view('homepage', ['results' => [], 'vocabs' => [], 'test_result' => $test_result, 'message' => null, 'word' => null, 'options' => null]);
    }
}
