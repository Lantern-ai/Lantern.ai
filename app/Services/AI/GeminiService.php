<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Prompts\Prompt;

class GeminiService
{

    public function __construct()
    {

    }

    public function askScript($prompt, $context){
        $sys_prompt = "You are a professional script writer and helper. You will be provided with a script summary, or relevant scenes from the script for the user prompt, etc.. and user will ask help or queries. You should provide the correct helpful answer to the user. Reply to the conversation below  : ";
        Log::info("Ask script");
        $prompt = "\n\n". "Script : \n". $context. "\n\nUser : ".$prompt;
        return $this->prompt($prompt,$sys_prompt);
    }

    public function editSelection($prompt,$context, $selection){
        $sys_prompt = "You are a professional script writer and helper. User will give you a part of the script and ask you to edit it as per their requirement. You should return the given part of the script edited as per the requirement. There will be a a user_selected_script feild and whole script in the input. You should return only the user selected script with required changes made.";
        $prompt =  "\n\n". "Script : \n". $context ."\n\nuser_selected_script : ".$selection. "\n\nUser : ".$prompt;

        return $this->prompt($prompt,$sys_prompt);
    }

    public function prompt($prompt,$sys_prompt)
    {


        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

        $payload = [
            'systemInstruction' => [
                "parts" => [
                    "text" => $sys_prompt,
                ]
            ],
            'contents' => [
                [
                    // role is optional; Google's examples often omit it
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        $response = Http::timeout(50)
            ->withHeaders([
                'Content-Type'   => 'application/json',
                'x-goog-api-key' => config('services.gemini.key'),
            ])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            // return the API’s error payload and HTTP status to the client
            return response()->json([
                'message' => 'Gemini API request failed',
                'error'   => $response->json(),
            ], $response->status());
        }

        $data = $response->json();

        // Safely pull out the first text candidate
        $output = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        return [
            'prompt' => $prompt,
            'output' => $output,
            // 'raw' => $data, // uncomment if you want full API response for debugging
        ];
    }
}
