<?php

namespace App\Http\Controllers;

use App\Models\Script;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Uri\Http;

class EditorManager extends Controller
{
    public function index(){

        return view('content.create');
    }
    public function create(Request $request){
        $script = Script::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'language'=> $request->language,
            'description' => $request->description ?? "",
        ]);


        return redirect()->route('script.editor', $script->id);
    }
    public function edit($id)
    {
        $content = Script::where('user_id', Auth::id())->where('id',$id)->first();
        if(!$content){
            abort(404);
        }
        return view('content.editor',compact('content'));
    }
    public function save(Request $request)
    {
        $script = Script::where('user_id', Auth::id())->where('id',$request->id)->first();


        $script->content = $request->body;
        $script->save();
        $payload = [
            'script_id' => (int) $script->id,
            'version'   => (string) ($request->version ?? 'v1'),
            // If you already have parsed scenes/blocks arrays, plug them in here:
            'scenes'    => $request->scenes ?? null, // each: {scene_id, slug?, text, chunk_id?}
            'blocks'    => $request->blocks ?? null, // each: {block_id, scene_id, type?, text}
        ];

        try {
            $base = "";
            $path = "";

            $response = Http::timeout(15)
                ->acceptJson()
                ->asJson()
                // If your FastAPI is HTTPS with a self-signed cert in dev, uncomment next line (dev only!)
                // ->withOptions(['verify' => false])
                ->post(rtrim($base, '/').$path, $payload);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'fastapi' => $response->json(),
                ]);
            }

            // Bubble up FastAPI error

        } catch (\Throwable $e) {

        }

        return response()->json(['success' => true]);
    }
}
