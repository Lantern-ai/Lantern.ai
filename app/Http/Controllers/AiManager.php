<?php

namespace App\Http\Controllers;

use App\Models\Script;
use App\Services\AI\GeminiService;
use App\Services\Script\ScriptParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiManager extends Controller
{
    private GeminiService $gemini;
    public function __construct(GeminiService $geminiService)
    {
        $this->gemini = $geminiService;
    }

    public function ask(Request $request){

        $script = Script::where('id',$request->script_id)->first();
        $scriptParser = new ScriptParser($script->content);
        $scriptText = $scriptParser->toFountain();
        Log::info($scriptText);
        if(isset($request->selected_text)){
            $response = $this->gemini->editSelection($request->prompt,$scriptText,$request->selected_text);
            Log::info("edit gemini");
            Log::info($response);
        }
else {
    $response = $this->gemini->askScript($request->prompt, $scriptText);
    Log::info("After gemini");
Log::info($response);
}
        return response()->json(
            ['response' => $response ]);
    }

    public function aiEdit()
    {

    }
//    public function mindMap(){
//        return view('mindmap');
//    }
}
