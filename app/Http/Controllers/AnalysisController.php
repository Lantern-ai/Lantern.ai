<?php

namespace App\Http\Controllers;

use App\Models\Script;
use App\Services\Script\ScriptParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalysisController extends Controller
{
    /**
     * Show the main analysis form.
     */

    public function __construct()
    {

    }

    public function show()
    {
        return view('analysis');
    }

    /**
     * Show the analysis page with script ID (UI loads immediately).
     */
    public function showAnalysis($script_id)
    {
        $script = Script::findOrFail($script_id);

        return view('analysis', [
            'script_id' => $script_id,
            'script_title' => $script->title ?? 'Untitled Script'
        ]);
    }

    /**
     * API endpoint: Fetch analysis results as JSON.
     */
    public function fetchAnalysis(Request $request)
    {
        try {
            // 1. Get the script
            Log::info("request all",$request->all());
            Log::info("Script id : ".$request->script_id);
            $script = Script::findOrFail($request->script_id);
            Log::info("Inisde fetch analysis".$script);
            // 2. Parse script content
            $scriptParser = new ScriptParser($script->content);
            $scriptText = $scriptParser->toFountain();

            // 3. Convert to nodes for Python API
            $nodes = $this->parseScriptToNodes($scriptText);

            // 4. Call Python API
            $api_url = config('services.script_analyzer.url', 'http://127.0.0.1:8080/analyze-scene/');
            Log::info("Inisde fetch analysis".$api_url);
            $response = Http::timeout(160)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($api_url, ['nodes' => $nodes]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Analysis service unavailable. Please ensure the Python server is running.'
                ], 503);
            }

            // 5. Return results
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse script text into nodes for the API.
     */
    private function parseScriptToNodes($scriptText)
    {
        return collect(preg_split("/\r\n|\n|\r/", $scriptText))
            ->filter(fn($line) => trim($line) !== '')
            ->map(function ($line) {
                $trimmedLine = trim($line);
                $type = 'action';

                // Detect scene headings
                if (Str::startsWith($trimmedLine, ['INT.', 'EXT.', 'EST.', 'INT./EXT.'])) {
                    $type = 'sceneHeading';
                }

                return [
                    'type' => $type,
                    'attrs' => new \stdClass(),
                    'content' => [['type' => 'text', 'text' => $trimmedLine]]
                ];
            })
            ->values()
            ->toArray();
    }
}
