<?php

namespace App\Services\Script;


class ScriptParser
{
    /** @var array<int,array<string,mixed>> */
    private array $nodes;

    /**
     * @param string|array $script JSON string or already-decoded array
     */
    public function __construct($script)
    {
        if (is_string($script)) {
            $decoded = json_decode($script, true);
        } else {
            $decoded = $script;
        }

        // Accept either an array of blocks or a doc with content
        if (isset($decoded['type']) && $decoded['type'] === 'doc' && isset($decoded['content'])) {
            $this->nodes = $decoded['content'] ?? [];
        } elseif (is_array($decoded)) {
            $this->nodes = $decoded;
        } else {
            $this->nodes = [];
        }
    }

    public function toFountain(): string
    {
        $out = [];
        $i = 0;
        $n = count($this->nodes);

        while ($i < $n) {
            $node = $this->nodes[$i];
            $type = $node['type'] ?? 'action';
            $text = $this->getNodeText($node);

            switch ($type) {
                case 'sceneHeading': {
                    $line = $this->normalizeSceneHeading($text);
                    if ($line !== '') {
                        $out[] = $line;
                        $out[] = ''; // blank line
                    }
                    $i++;
                    break;
                }

                case 'character': {
                    $character = $this->normalizeCharacter($text);
                    if ($character !== '') {
                        $out[] = $character;

                        // Collect optional parenthetical and one or more dialogue blocks
                        $i++;
                        $consumedAny = false;
                        while ($i < $n) {
                            $next = $this->nodes[$i];
                            $nt   = $next['type'] ?? '';
                            $t    = $this->getNodeText($next);

                            if ($nt === 'parenthetical') {
                                $out[] = $this->normalizeParenthetical($t);
                                $i++; $consumedAny = true;
                                continue;
                            }
                            if ($nt === 'dialogue') {
                                foreach ($this->splitLines($t) as $dl) {
                                    $out[] = rtrim($dl);
                                }
                                $i++; $consumedAny = true;
                                continue;
                            }
                            break; // leave dialogue run
                        }

                        // Blank line after a dialogue block set
                        if ($consumedAny) {
                            $out[] = '';
                        }
                    } else {
                        $i++; // skip empty
                    }
                    break;
                }

                case 'dialogue': {
                    // Dialogue without a preceding character: emit as action fallback
                    foreach ($this->splitLines($text) as $dl) {
                        $out[] = rtrim($dl);
                    }
                    $out[] = '';
                    $i++;
                    break;
                }

                case 'parenthetical': {
                    $out[] = $this->normalizeParenthetical($text);
                    $out[] = '';
                    $i++;
                    break;
                }

                case 'transition': {
                    $line = $this->normalizeTransition($text);
                    if ($line !== '') {
                        // In Fountain, transitions are recognized if they’re ALL CAPS and end with "TO:"
                        $out[] = $line;
                        $out[] = '';
                    }
                    $i++;
                    break;
                }

                case 'action':
                case 'paragraph':
                default: {
                    // Treat generic paragraphs as Action
                    $lines = $this->splitLines($text);
                    if (!empty($lines)) {
                        foreach ($lines as $ln) {
                            $ln = rtrim($ln);
                            if ($ln !== '') {
                                $out[] = $ln;
                            }
                        }
                        $out[] = '';
                    }
                    $i++;
                    break;
                }
            }
        }

        // Trim trailing blank lines
        while (!empty($out) && end($out) === '') {
            array_pop($out);
        }
        return implode("\n", $out) . "\n";
    }

    /* ---------------- Helpers ---------------- */

    private function getNodeText(array $node): string
    {
        if (!isset($node['content']) || !is_array($node['content'])) {
            return '';
        }
        $buf = [];
        $stack = $node['content'];

        while (!empty($stack)) {
            $item = array_shift($stack);
            if (!is_array($item)) continue;

            if (($item['type'] ?? '') === 'text') {
                $buf[] = (string) ($item['text'] ?? '');
            }
            if (isset($item['content']) && is_array($item['content'])) {
                // depth-first: push children to stack
                foreach ($item['content'] as $child) {
                    $stack[] = $child;
                }
            }
        }
        return trim(implode('', $buf));
    }

    private function normalizeSceneHeading(string $s): string
    {
        $s = trim($s);
        if ($s === '') return '';
        $upper = strtoupper($s);

        // If it already looks like a standard slug (INT./EXT./INT/EXT/I/E)
        if (preg_match('/^(INT\.|EXT\.|INT\/EXT\.|I\/E\.)\s/', $upper)) {
            return $upper;
        }
        // Force as a scene heading if not matching canonical tokens.
        // In Fountain, a leading dot forces a scene heading.
        return '.' . $s;
    }

    private function normalizeCharacter(string $s): string
    {
        $s = trim($s);
        if ($s === '') return '';
        return strtoupper($s);
    }

    private function normalizeParenthetical(string $s): string
    {
        $s = trim($s);
        if ($s === '') return '( )';
        // Ensure wrapped in parentheses
        if ($s[0] !== '(' || substr($s, -1) !== ')') {
            $s = '(' . $s . ')';
        }
        return $s;
    }

    private function normalizeTransition(string $s): string
    {
        $s = strtoupper(trim($s));
        if ($s === '') return '';
        // Ensure colon if it ends with TO (common)
        if (preg_match('/\bTO$/', $s)) {
            $s .= ':';
        }
        // Fountain recognizes TRANSITION if ALL CAPS and ends with "TO:" or is FADE IN:/OUT:
        return $s;
    }

    /** @return string[] */
    private function splitLines(string $s): array
    {
        $s = str_replace(["\r\n", "\r"], "\n", $s);
        $parts = explode("\n", $s);
        // trim right but not left (keep leading spaces if any)
        return array_map(fn($x) => rtrim($x, "\t "), $parts);
    }
}
