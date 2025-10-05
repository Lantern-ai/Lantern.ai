<?php

namespace App\Http\Controllers;

use App\Models\Script;
use App\Services\AI\GeminiServiceStatic;
use Closure;


use Illuminate\Http\Request;

use Graphp\Graph\Graph;
use Graphp\GraphViz\GraphViz;


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
    public function viewMindmap($script_id) {

        // return view('components.mindmap', ["mindmap" => $this->generateChrRelationMm()]);
        return view('mindmap', ["mindmap" => $this->gcrmNoWait($script_id),"script_id" => $script_id]);
    }

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
        $mindMapHtml = Script::where('id', $script_id)->first()->characterMindMap;

        return $mindMapHtml;
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
        $graphviz->setExecutable('C:\\Program Files\\Graphviz\\bin\\dot.exe');
        $mcNode = $graph->createVertex()->setAttribute('id', $mc);
        $mcNode->setAttribute("fontsize", "20pt");

        $node_array = array();

//dd($outputText);
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

        return $mindMapHtml;
    }




    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.mindmap');
    }
}
