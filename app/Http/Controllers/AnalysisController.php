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

        $graph_data = $this->analyze3Act($script);

        return view('analysis', [
            'script_id' => $script_id,
            'script_title' => $script->title ?? 'Untitled Script',
            'graph_data' => $graph_data ?? ''
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
            $api_url = "http://127.0.0.1:8080/analyze-scene/";
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

    public function analyze3Act(Script $script)
    {

        Log::info("Inisde analyze3act".$script);
        // Your DB may store the whole doc or just the nodes.
        // Try to decode and pull out the `content` array either way.
        $raw = $script->content; // could be array or JSON string

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            }
        }

        // If it's a Tiptap doc { type: 'doc', content: [...] }, extract nodes.
        if (is_array($raw) && isset($raw['type']) && isset($raw['content'])) {
            $contentNodes = $raw['content'];
        } else {
            // If already an array of nodes, use it; otherwise null.
            $contentNodes = is_array($raw) ? $raw : null;
        }

        // Build payload for the single `/analyze` endpoint.
        // If we couldn’t obtain nodes, fall back to plain text (if you store it).
        $payload = [
            // Prefer content (Tiptap nodes) when available:
            'content'        => $contentNodes,
            // Optional: if you also store a plain text version, send it as fallback:

            // 'weights'      => ['action'=>0.25,'conflict'=>0.2,'stakes'=>0.15,'urgency'=>0.15,'arousal'=>0.15,'pacing'=>0.1],
        ];

        // Clean out nulls so FastAPI receives only what’s relevant
        $payload = array_filter($payload, fn($v) => !is_null($v));

        // Point to your FastAPI server (adjust if you kept a different route).
        $url = "http://127.0.0.1:8080/analyze-three-act";

        try {
            $res = Http::timeout(30)->acceptJson()->post($url, $payload);
            Log::info('Analyze3Act response: '.$res->body());
        } catch (\Throwable $e) {
//            Log::error('Analyze3Act HTTP error: '.$e->getMessage(), ['script_id' => $script_id]);
            return [
                'error' => 'Analyzer service unreachable',
                'message' => $e->getMessage(),
            ];
        }

        if (!$res->successful()) {
            Log::warning('Analyze3Act non-2xx', ['status' => $res->status(), 'body' => $res->body()]);
            return [
                'error'  => 'Analyzer failed',
                'status' => $res->status(),
                'body'   => $res->json() ?? $res->body(),
            ];
        }

        // Success: return the analyzer’s plot-ready payload
        return $res->json();
    }
}
