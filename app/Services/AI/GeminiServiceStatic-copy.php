<?php

namespace App\Services\AI;

use App\Models\Script;
use App\Services\Script\ScriptParser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GeminiServiceStatic
{

    public function __construct()
    {

    }

    public static function askScript($prompt, $context){
        $sys_prompt = "You are a professional script writer and helper. You will be provided with a script summary, or relevant scenes from the script for the user prompt, etc.. and user will ask help or queries. You should provide the correct helpful answer to the user. Reply to the conversation below  : ";

        $prompt = $sys_prompt . "\n\n". $context. "\n\nUser : ".$prompt;
        return GeminiServiceStatic::prompt($prompt);
    }

    public static function askScriptNoSysPrompt($prompt, $context) {

        $prompt = $context. "\n\nUser : ".$prompt;
        return GeminiServiceStatic::prompt($prompt);

    }

    public static function prompt($prompt)
    {


        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

        $payload = [
            'systemInstruction' => "You are a professional script writer and helper. You will be provided with a script summary, or relevant scenes from the script for the user prompt, etc.. and user will ask help or queries. You should provide the correct helpful answer to the user. Reply to the conversation below  : ",
            'contents' => [
                [
                    // role is optional; Google's examples often omit it
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        $response = Http::timeout(20)
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

    public static function structuredAskChrRelationMm($scriptId) {

        $prompt = "Find the main character from the script and give the relationships of the character towards other characters, including a short description.";


        $queryResult = Script::where("id", $scriptId)->where("user_id", Auth::user()->id)->first()->content;
        $sp = new ScriptParser($queryResult);

        $context = $sp->toFountain();

//         $context = "സീന്‍
// -
// 1
//  ള്ളിക്കുന്നന ള്ളി മമ ോ ം-ടസ്റ്റജ് NIGHT/EXT
// ഫ്രെയിമില്‍ സൂ
// പ്പര്‍മാനോടാ സ സാമ
// നോ ാന്നിപ്പികുനസന്ന ്തിലിളസള ഒ ു്സ
// ആള്‍്ൂപം. ദ ശ്യം റസ്ചുകൂറൂ ി
// വ് ക്തമാറസനോപാള്‍ അത്
// ‘പം.ള ഒികുനസന്നിഫ്ള
// പം.സണ് ാളന്‍’ എന്ന ടാ റതിലിഫ്െ റൂറന്ന്‍ ു്സ
// റഫ്ടൌട് ആണ്ദ
// സൂ
// പ്പര്‍മാഫ്െ നോറപ്പിടസ സമാടമായ ു്സ
// ചസവ്ന്ന സണ്ിഫ്റാണ്ട് മസഖ മ്ചുകൂ മസിളസ
// ഫ്പം.്സപ്പിചുക് സൂപ്പര്‍ ഹീനോ്ാ ളാന്‍‍ി ങ്്
// ഫ്പം.ാസിഷടില്‍ ടില്‍കുനസന്ന പം.സണ് ാളഫ്െ റഫ്ടൌട്ദ
// റഫ്ടൌടിടസ ാഫ്െ ഇങ്ഫ്ട
// എെസ ിയി്ികുനസന്നസദ “് ഗറള അവ് ്ിപ്പികുനസന്ന മാര്‍ടി
// ന്‍
// പം.ള ഒികുനസന്നിഫ്െ ടാ റ
// ‘പം.ള ഒികുനസന്നിഫ്ള
// പം.സണ് ാളന്‍
// ’ മാര്‍ചുക് 14ടസ പം.ള ഒികുനസന്ന്
// പം.ള ഒിയസഫ് മമ ാടതില് രപം.ശര്‍യംിപ്പികുനസന്നസദ
// ഏവ്ര്‍കുനസ സ ാഗ ”.
// പം.ശ്ചാതിലളതിലില്‍ ഫ്ളൌഡ് സ്പം.ീകുന്ിളൂഫ്
// മസെങ്ി നോറള്‍കുനസന്ന രപം.സ് സ ടാ റതിലിഫ്െ
// യംബ്ദ ദ
// റഫ്ടൌടിഫ്ള വ്ാചറങ്ള്‍
// വ്ായിചുകൂഫ്റാണ്ടി്ികുനസന്ന ു്സ പം.യ്യന്‍ (1
// 4
// വ്യസ്സ്)ദ അവ്ഫ്െ ചസണ്ടില്‍ ു്സ ബീ‍ി
// എ്ിയസന്നസണ്ട്ദ ടാ റതിലിഫ്െ യംബ്ദ നോറടൂ അവ്ന്‍ വ്ളിചുകൂഫ്റാണ്ടി്സന്ന ബീ‍ി അവ്ിഫ്
// ടിളനോതിലകുന് എ്ിഞ്ഞിട് നോപം.ാറസന്നസദ
// ശ്യം ടിളതിലസ റി ന്ന് എ്ിയസന്ന ബീ‍ി
// റസറന്ിയില്‍ദ അ ില്‍ടിന്നസ ടിളതിലസറി കുനസന്ന
// ു്സ ഉണ്ങ്ിയ ഓളയിനോളകുന് ഫ്മഫ്ളെ ീ
// പം.ി ികുനസന്നസദ ീ റതിലി ഫ്മഫ്ളെ റയ്സനോപാള്‍
// ശ്യം അ ിടസ സമീപം. ഉള ഒ
// ഫ്വ് ിപ്പൂ്യിനോളകുന് ടീങ്സന്നസദ
// Cut T
// o
// ഷിബസ പം.യ്യന്‍ (1
// 4 വ്യസ്സ്)
// 3
// സീന്‍ - 1A
//  ള്ളിക്കുന്നന ള്ളി മമ ോ ം -ടസ്റ്റജ് NIGHT-EXT
// ആനോവ്യംതിലില്‍ ടാ റ റണ്ടസഫ്റാണ്ട്
// ടിളതിലി്ികുനസന്ന റസ്ചുക് റസടിറള്‍ദ ആ
// റൂടതിലില്‍ ആ്സവ്യസ്സൂറാ്ന്‍ ഫ്െയ്സടസ
// ഉണ്ട്ദ അവ്ഫ്െ വ്ളനോതില റയ്യില്‍ടിന്നസ
// അളിഞ്ഞസ വ്ീണ്സഫ്റാണ്ടി്ികുനസന്ന ഐസ്
// മിടായിദ
// നോേെില്‍ ടിന്നസള ഒ പം.ചുകയസ ചസവ്പ്പൂ റളര്‍ന്ന
// ഫ്വ്ളിചുക ഫ്െയ്സഫ്െ മസഖതില് വ് ക്തമായി
// മിന്നി മ്യസന്നസണ്ട്ദ ടാ റതിലിഫ്ള ് ഗ
// ട കുനസന്നത് സന്ധ് ാസമയതിലാണ്ദ നോേെില്‍ ു്സ
// ഇളെതിലിഫ്െ മസന്‍വ്യം ഫ്സറന്് ഇടി്ികുനസന്നസദ
// അ ിടസ മസന്നിളായി ു്സ ചാ്സറനോസ്യില്‍
// ഇ്ികുനസന്ന െന്മിദ മഫ്റന്ാ്സവ്യംതില്
// വ്്ിവ്്ിയായി ടില്‍കുനസന്ന റര്‍ഷറര്‍ദ
// അവ്ഫ്് ഓനോ്ാ്സതിലനോ്യസ അ ിമസ ി
// പം.്ിനോയംാധികുനസന്ന െന്മിയസഫ് റീൊളര്‍ദ
// ഭീ ിനോയാഫ് ടില്‍കുനസന്ന റര്‍ഷറര്‍ദ
// പം.്ിനോയംാധടകുനി യില്‍ നോറളൂ എന്ന
// റര്‍ഷറഫ്െ അ്ഫ്കുനടില്‍ ടിന്നസ ു്സ റിെി
// ഫ്ടളെ് പം.ി ിഫ്ചുക സകുനസന്ന റീൊളര്‍ദ അത് റണ്ടിട്
// നോറളൂവ്ിഫ്െ അ സനോതിലകുന് അള്ി
// അ സതിലസഫ്റാണ്ട്
//  ന്മി :-
// അയാള്‍ നോറളൂവ്ിഫ്െ ഫ്ടചത്തില് ചവ്ിടൂന്നസദ
// ആ ചവ്ിടില്‍ നോറളൂ ഫ് ്ിചുക് വ്ീെസന്നസദ
// റ്ഞ്ഞസ വ്ിളിചുകൂഫ്റാണ്ട്
// ടേളു :-
// നോറളൂ പം.്യസന്നത് നോറള്‍കുനാഫ് െന്മി വ്ീണ്ടസ
// അവ്ഫ്ട ചവ്ിടൂന്നസദ അവ്ിനോ കുന്
// വ്ിങ്ിഫ്പ്പാടിഫ്കുനാണ്ട് ഓ ി വ്്സന്ന
// നോറളൂവ്ിഫ്െ ഭാ് യസ റസഞ്ഞസ ദ അവ്ഫ്്
//  യസന്ന റീൊളര്‍ദ ടാ റ റാണ്സന്ന
// െടങ്ളൂഫ് ്ിയാക്ഷന്‍ദ ചിള സ്ര ീറള്‍
// അ സറണ്ട് ഫ്പം.ാടികുന്യസന്നസദ ചിള
// പം.സ്സഷന്മാര്‍ നോശഷ തിലില്‍ ങ്ളൂഫ് മസഷ്ടി
// ചസ്സടൂന്നസദ ഫ്െയ്സടസ റ്ചുകിളിഫ്െ
//  ഫ്െയ്സന്‍(6),മാര്‍ടിന്‍,വ്ര്‍കുനി(35)
//  െന്മി,നോറളൂ,ര‍ാമ ീ
//  മാര്‍ടിന്‍ ്ക്ഷികുനസന്ന റസടി
// റാണ്ിറളില്‍ ു്ാള്‍,
//  ടാ റ റാണ്സന്ന റസടിറള്‍,ടാടൂറാര്‍
// റകുനസനോന്നാ ടാനോയദദ
// ്ണ്ടീസായി റസ ീല് പം.ടിണ്ിയാണ് ഏമാനോടദദ
// 4
// വ്കുനതിലാണ്ദ ു്സ വ്ാള്‍ എ സതിലസ റീൊളഫ്െ
// നോടഫ്് ടീടി
//  ന്മി :-
// റീൊളര്‍ നോറളൂവ്ിഫ്െ മററള്‍ വ്ളിചുക്
// ടീടൂന്നസദ ു്ാള്‍ വ്ാഫ്ള സതില് നോറളൂവ്ിഫ്െ
// മറ ഫ്വ്ടൂവ്ാടായി സ ങ്സന്നസദ ടാ റ
// റാണ്സന്നവ്്സഫ് ്ിയാക്ഷന്‍ദ
// ഫ്പം.ഫ്ടന്ന് നോേെില്‍ ു്സ വ്ളിയ രപം.റായം
// ടി്യസന്നസദ അത് റാണ്സനോപാള്‍
// ആനോവ്യംഭ്ി ്ാവ്സന്ന ടാടൂറാര്‍
// ആര്‍പ്പൂവ്ിളികുനസന്നസദ ഫ്െയ്സടസ ുപ്പ ഉണ്ട്ദ
// നോേെിനോളകുന് ഗീവ്ര്‍ഗീസ് പം.സണ് ാളഫ്െ
// നോവ്ഷതിലില്‍ മസഖമൂ ി ധാ്ിയായ ു്ാള്‍
// പം.്ന്ന് എതിലസന്നസദ അയാള്‍ അ ിയാളഫ്്യസ
// െന്മിയസ അ ിചുക് ടിള പം.്ിയംാകുനി,
// സൂപ്പര്‍ഹീനോ്ാ ളാന്‍‍ി ഗ് പൊ ഫ്പം.ാസിഷടില്‍
// ടില്‍കുനസന്നസദ റണ്ടസഫ്റാണ്ടി്സന്ന ആളൂറള്‍
// എെസനോന്നറന്് മറ അ ികുനസറയസ ആര്‍പ്പ്
// വ്ിളികുനസറയസ ഫ്ചയ്യൂന്നസദ
// ഫ്പം.ഫ്ടന്ന് റാണ്ിറളൂഫ് ഇ യില്‍ ടിന്ന് ു്ാള്‍
// ടിളവ്ിളിചുകൂഫ്റാണ്ട് ഓ ിവ്്സന്നസദ
// ആള്‍ :-
// അയാള്‍ പം.്ഞ്ഞ് മസെസവ്ിപ്പികുനസ മസന്‍്
// ഫ്വ് ിപ്പൂ് ഫ്പം.ാടി ഫ് ്ികുനസന്നസദ ടാടൂറാ്സഫ്
// ്ിയാക്ഷന്‍ദ അ സറണ്ട് ു്സ ടിമിഷ എന്ത്
// ഫ്ചയ്യണ് എന്ന് അ്ിയാഫ് ഫ്െടിതില്ിചുകൂ
// ടില്‍കുനസന്ന മസഖ മൂ ിധാ്ിദ രഗൌണ്ടില്‍
// പം.ളയി തിലായി വ്ീണ്ടസ ഫ്പം.ാടിഫ്തില്ിറള്‍
// ഉണ്ടാവ്സന്നസദ ടാടൂറാര്‍ പം.ളവ്െികുനായി ഓ ി
//  സ ങ്സന്നസ, ആള്‍കുനൂടതിലിടസ ഇ യില്‍
// ഫ്െയ്സന്‍, അവ്ഫ്െ റാചയചയില്‍ നോേജ്
// ഫ്പം.ാടിഫ്തില്ികുനസന്നസദ
// ഫ്െയ്സഫ്െ റണുകൂറളില്‍ ടിന്നസ ശ്യം fade
// out ആറസന്നസദ
// Fade inറതിലിയമര്‍ന്ന് റി കുനസന്ന നോേെസ പം.ള ഒി
// മമ ാടവ്സ ദ നോേെിടസള ഒില്‍
// റതിലികുന്ിഞ്ഞസഫ്റാണ്ട് ഇ്ികുനസന്ന
// മസഖ മൂ ിധാ്ി ധ്ിചുകി്സന്ന മസഖ മൂ ിദ
// മസഖ മൂ ിയിനോളകുന് ഫ്മഫ്ളെ റ ാമ്
// ഇടി അവ്ന്‍ റകുന്സത്ദദ
// ഓ ിനോകുനാ ദദ ഫ്വ് ിപ്പൂ്കുന് ീപം.ി ിചുകൂദദ
// 5
// ടീങ്സനോപാള്‍ ബില്‍ഡ് അപ്പ് മ ൂസി
// റടകുനസന്നസ
// .
// Cut To
// നോമഘങ്ള്‍കുന് ഇ യില്‍ടിന്നസ ശ്യം ദ അവ്ിഫ്
// അങ്ിങ്ായി ഇ ിമിന്നല്‍ ളിയസന്നസണ്ട്ദ
// ഇ ിമസെകുനതിലിഫ്െ യംബ്ദ
// ദ
// സ്രറീടില്‍
// ‘WEEKEND BLOCKBUSTERS’
// അവ് ്ിപ്പികുനസന്ന മിന്നല്‍ മസ്ളി എന്ന
// ചിര തിലിഫ്െ മ റന്ില്‍ ഫ് ളിയസന്നസദ";

        // $context = "John is the father of Henry";

        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

        $payload = [
            'systemInstruction' => [
                "parts" => [
                    "text" => "You are a professional script writer and helper. You will be provided with a script summary, or relevant scenes from the script for the user prompt, etc.. and user will ask help or queries. You should provide the correct helpful answer to the user. Reply to the conversation below:"
                ]
            ],
            'contents' => [
                [
                    // role is optional; Google's examples often omit it
                    'parts' => [
                        ['text' => $context . "\n\n" . $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                "responseMimeType" => "application/json",
                "responseSchema" => [
                    "type" => "OBJECT",
                    "properties" => [

                        "mainCharacter" => [
                            "type" => "STRING"
                        ],

                        "relations" => [
                            "type" => "ARRAY",
                            "items" => [
                                "type" => "OBJECT",
                                "properties" => [

                                    "characterName" => [
                                        "type" => "STRING"
                                    ],

                                    "characterRelation" => [
                                        "type" => "STRING"
                                    ],

                                    "characterRelationDescription" => [
                                        "type" => "STRING"
                                    ]
                                ],
                                "propertyOrdering" => ["characterName", "characterRelation", "characterRelationDescription"]
                            ]
                        ]
                    ],
                    "propertyOrdering" => ["mainCharacter", "relations"]
                ]
            ]
        ];

        $response = Http::timeout(200)
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

        // Safely pull out the first text candidate
        // $output = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        $output = $response->json();

        return [
            'prompt' => $prompt,
            'output' => $output,
            // 'raw' => $data, // uncomment if you want full API response for debugging
        ];

    }
}
