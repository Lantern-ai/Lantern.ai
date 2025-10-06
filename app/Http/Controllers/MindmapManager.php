<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiServiceStatic;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Graphp\Graph\Graph;
use Graphp\GraphViz\GraphViz;
use App\Models\Script;

class MindmapManager extends Controller
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }



    // public function generateMindmap(Request $request) {

    //     if ($request->has("option")) {
    //         $option = $request->option;
    //     } else {
    //         $option = 1;
    //     }

    //     switch($option) {
    //         case 1:
    //             return $this->generateChrRelationMm();
    //             break;
    //         }
    // }

    private function generateChrRelationMm() {

        $mindMapHtml = Script::where('id', 1)->first()->characterMindMap;

        if ($mindMapHtml != null) {
            return $mindMapHtml;
        }

        $output = GeminiServiceStatic::structuredAskChrRelationMm();
        $outputText = $output["output"]['candidates'][0]['content']['parts'][0]['text'];

        $outputArr = json_decode($outputText, true);

        $mc = $outputArr["mainCharacter"];

        $relations = $outputArr["relations"];

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.layout', 'twopi');
        $graph->setAttribute('graphviz.graph.overlap', 'scale');
        $graphviz = new GraphViz();
        $graphviz->setFormat('svg');

        $mcNode = $graph->createVertex()->setAttribute('id', $mc);
        $mcNode->setAttribute("fontsize", "20pt");

        $node_array = array();


        foreach ($relations as $relation) {

            $desc = $relation["characterRelationDescription"];

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $desc = preg_replace($pattern, $replacement, $desc);

            $chr = $graph->createVertex()->setAttribute('id', $relation["characterName"]);

            $chr->setAttribute('graphviz.shape', 'none');

            // $chr->setAttribute("width", "0.5in");
            $chr->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee"><b>\N</b></td></tr>
            <tr><td>' . $relation["characterRelation"] . '</td></tr>
            <tr><td style="max-width: 100px;">' . $desc . '</td></tr>
        </table>');

            $node_array[] = $chr;

            $graph->createEdgeDirected($mcNode, $chr);
        }

        $script = Script::where('id', 1)->first();
        $mindMapHtml = $graphviz->createImageHtml($graph);
        $script->characterMindMap = $mindMapHtml;
        $script->save();

        return $mindMapHtml;
    }

    private function gcrmNoWait($script_id) {
        $mindMapData = Script::where('id', $script_id)->first();
        $mindMapHtml = $mindMapData->characterMindMap;

        return ["mindmap" => $mindMapHtml, "relations" => json_decode($mindMapData->characters)];
    }

    public function generateChrRelationMmRegen(Request $request) {

        $output = GeminiServiceStatic::structuredAskChrRelationMm($request->script_id);
        $outputText = $output["output"]['candidates'][0]['content']['parts'][0]['text'];

        $outputArr = json_decode($outputText, true);

        $mc = $outputArr["mainCharacter"];

        $relations = $outputArr["relations"];

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.layout', 'twopi');
        $graph->setAttribute('graphviz.graph.overlap', 'scale');
        $graphviz = new GraphViz();
        $graphviz->setFormat('svg');

        $mcNode = $graph->createVertex()->setAttribute('id', $mc);
        $mcNode->setAttribute("fontsize", "20pt");

        $node_array = array();


        foreach ($relations as $relation) {

            $desc = $relation["characterRelationDescription"];

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $desc = preg_replace($pattern, $replacement, $desc);

            $chr = $graph->createVertex()->setAttribute('id', $relation["characterName"]);

            $chr->setAttribute('graphviz.shape', 'none');

            // $chr->setAttribute("width", "0.5in");
            $chr->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee"><b>\N</b></td></tr>
            <tr><td>' . $relation["characterRelation"] . '</td></tr>
            <tr><td style="max-width: 100px;">' . $desc . '</td></tr>
        </table>');

            $node_array[] = $chr;

            $graph->createEdgeDirected($mcNode, $chr);
        }

        $script = Script::where('id', $request->script_id)->first();
        $mindMapHtml = $graphviz->createImageHtml($graph);
        $script->characterMindMap = $mindMapHtml;
        $script->save();
        $relations = $outputArr["relations"];
        $script->characters = $relations;
        $script->save();

        return ["html" => $mindMapHtml, "relations" => $relations];
    }


    public function generateChrRelationMmRegenWithCharacter(Request $request) {

        $character = $request->character;
        $output = GeminiServiceStatic::structuredAskChrRelationMmWithCharacter($character,$request->script_id);
        $outputText = $output["output"]['candidates'][0]['content']['parts'][0]['text'];

        $outputArr = json_decode($outputText, true);

        $mc = $outputArr["mainCharacter"];

        $relations = $outputArr["relations"];

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.layout', 'twopi');
        $graph->setAttribute('graphviz.graph.overlap', 'scale');
        $graphviz = new GraphViz();
        $graphviz->setFormat('svg');

        $mcNode = $graph->createVertex()->setAttribute('id', $mc);
        $mcNode->setAttribute("fontsize", "20pt");

        $node_array = array();


        foreach ($relations as $relation) {

            $desc = $relation["characterRelationDescription"];

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $desc = preg_replace($pattern, $replacement, $desc);

            $chr = $graph->createVertex()->setAttribute('id', $relation["characterName"]);

            $chr->setAttribute('graphviz.shape', 'none');

            // $chr->setAttribute("width", "0.5in");
            $chr->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee"><b>\N</b></td></tr>
            <tr><td>' . $relation["characterRelation"] . '</td></tr>
            <tr><td style="max-width: 100px;">' . $desc . '</td></tr>
        </table>');

            $node_array[] = $chr;

            $graph->createEdgeDirected($mcNode, $chr);
        }

        $script = Script::where('id', $request->script_id)->first();
        $mindMapHtml = $graphviz->createImageHtml($graph);
        $script->characterMindMap = $mindMapHtml;
        $script->save();
        $relations = $outputArr["relations"];
        $script->characters = $relations;
        $script->save();

        return ["html" => $mindMapHtml, "relations" => $relations];

    }

    // public function generatePacingMindMapHandler(Request $request) {
    //     $script_id = $request->script_id;
        // $mindMap = Scripts::where('id', $script_id)->first();

    //     return ["mindMap" => $mindMap];
    // }

    public function generatePacingMindMap(Request $request) {
        $script_id = $request->script_id;

        $mindMap = Script::where('id', $script_id)->first();

        $pacingMindmap = $mindMap->pacingMindmap;

        if ($pacingMindmap != null) {
            return ["html" => $pacingMindmap];
        }

        $output = GeminiServiceStatic::genPaceMap($script_id);

        $outputText = $output["output"]['candidates'][0]['content']['parts'][0]['text'];

        $outputArr = json_decode($outputText, true);

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.layout', 'twopi');
        $graph->setAttribute('graphviz.graph.overlap', 'scale');
        $graphviz = new GraphViz();
        $graphviz->setFormat('svg');

        $centerNode = $graph->createVertex()->setAttribute('id', 'Pacing Issues');
        $centerSlowNode = $graph->createVertex()->setAttribute('id','Slow Paced');
        $centerFastNode = $graph->createVertex()->setAttribute('id','Fast Paced');

        $graph->createEdgeDirected($centerNode, $centerSlowNode);
        $graph->createEdgeDirected($centerNode, $centerFastNode);

        foreach ($outputArr["slowPaced"] as $slowNodeData) {
            $scene = $slowNodeData;

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $scene = preg_replace($pattern, $replacement, $scene);

            $node = $graph->createVertex()->setAttribute('id', $scene);
            $node->setAttribute('graphviz.shape', 'none');

            $node->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee">'. $scene . '</td></tr>
        </table>');

            $graph->createEdgeDirected($centerSlowNode, $node);
        }

        foreach ($outputArr["fastPaced"] as $fastNodeData) {
            $scene = $fastNodeData;

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $scene = preg_replace($pattern, $replacement, $scene);

            $node = $graph->createVertex()->setAttribute('id', $scene);

            $node->setAttribute('graphviz.shape', 'none');

            $node->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee">\N</td></tr>
        </table>');

            $graph->createEdgeDirected($centerFastNode, $node);
        }

        $pacingMindmapNewHtml = $graphviz->createImageHtml($graph);

        $mindMap->pacingMindmap = $pacingMindmapNewHtml;
        $mindMap->save();

        return ["html" => $pacingMindmapNewHtml];

    }

    public function forceGeneratePacingMindMap(Request $request) {
        $script_id = $request->script_id;

        $mindMap = Script::where('id', $script_id)->first();

        $pacingMindmap = $mindMap->pacingMindmap;

        $output = GeminiServiceStatic::genPaceMap($script_id);

        $outputText = $output["output"]['candidates'][0]['content']['parts'][0]['text'];

        $outputArr = json_decode($outputText, true);

        $graph = new Graph();
        $graph->setAttribute('graphviz.graph.layout', 'twopi');
        $graph->setAttribute('graphviz.graph.overlap', 'scale');
        $graphviz = new GraphViz();
        $graphviz->setFormat('svg');

        $centerNode = $graph->createVertex()->setAttribute('id', 'Pacing Issues');
        $centerSlowNode = $graph->createVertex()->setAttribute('id','Slow Paced');
        $centerFastNode = $graph->createVertex()->setAttribute('id','Fast Paced');

        $graph->createEdgeDirected($centerNode, $centerSlowNode);
        $graph->createEdgeDirected($centerNode, $centerFastNode);

        foreach ($outputArr["slowPaced"] as $slowNodeData) {
            $scene = $slowNodeData;

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $scene = preg_replace($pattern, $replacement, $scene);

            $node = $graph->createVertex()->setAttribute('id', $scene);
            $node->setAttribute('graphviz.shape', 'none');

            $node->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee">'. $scene . '</td></tr>
        </table>');

            $graph->createEdgeDirected($centerSlowNode, $node);
        }

        foreach ($outputArr["fastPaced"] as $fastNodeData) {
            $scene = $fastNodeData;

            $pattern = '/((\S+\s+){4})(?=.)/';

            // Replacement explanation:
            // $1           : Refers to the full match (the four words and their trailing space)
            // \n           : The newline character
            $replacement = '$1<BR/>';

            $scene = preg_replace($pattern, $replacement, $scene);

            $node = $graph->createVertex()->setAttribute('id', $scene);

            $node->setAttribute('graphviz.shape', 'none');

            $node->setAttribute('graphviz.label_html', '
        <table cellspacing="0" border="0" cellborder="1">
            <tr><td bgcolor="#eeeeee">'. $scene . '</td></tr>
        </table>');

            $graph->createEdgeDirected($centerFastNode, $node);
        }

        $pacingMindmapNewHtml = $graphviz->createImageHtml($graph);

        $mindMap->pacingMindmap = $pacingMindmapNewHtml;
        $mindMap->save();

        return ["html" => $pacingMindmapNewHtml];

    }


    public function viewMindmap($script_id) {
        $script = Script::where('id', $script_id)->first();
//        dd(["script_id" => $script_id, "script"=>$script]+$this->gcrmNoWait($script_id));

        // return view('components.mindmap', ["mindmap" => $this->generateChrRelationMm()]);
        return view('mindmap', ["script_id" => $script_id, "script"=>$script]+$this->gcrmNoWait($script_id));
    }


    /**
     * Get the view / contents that represent the component.
     */

}
