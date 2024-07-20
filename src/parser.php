<?php
function parse($code)
{
    $lines = explode("\n", $code);
    $tokens = [];
    $inPHP = false;
    $inHTML = false;
    foreach ($lines as $line) {
        if (preg_match('/<\?php/', $line)) {
            $tokens[] = ['type' => 'php_start'];
            $inPHP = true;
        } elseif (preg_match('/<\?html/', $line)) {
            $tokens[] = ['type' => 'html_start'];
            $inHTML = true;
        } elseif (preg_match('/\?>/', $line)) {
            if ($inPHP) {
                $tokens[] = ['type' => 'endphp'];
                $inPHP = false;
            } elseif ($inHTML) {
                $tokens[] = ['type' => 'html_end'];
                $inHTML = false;
            }
        } elseif (!$inPHP && !$inHTML) {
            if (preg_match('/print\s+(.*)/', $line, $matches) || preg_match('/echo\s+(.*)/', $line, $matches)) {
                $tokens[] = ['type' => 'print', 'value' => $matches[1]];
            } elseif (preg_match('/match\s+"([^"]+)"\s+with\s+"([^"]+)"(?:\s+as\s+([a-zA-Z_][a-zA-Z0-9_]*))?/', $line, $matches)) {
                $token = [
                    'type' => 'match',
                    'pattern' => $matches[1],
                    'string' => $matches[2]
                ];
                // Check if an 'as' statement is present and capture the variable name
                if (isset($matches[3])) {
                    $token['as'] = $matches[3];
                }
                $tokens[] = $token;
            } elseif (preg_match('/\/\/(.*)/', $line, $matches)) {
                $tokens[] = ['type' => 'comment', 'value' => $matches[1]];
            } elseif (trim($line) === '') {
                // Ignore empty lines

            } elseif /* Variables */ (preg_match('/let\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(.*)/', $line, $matches)) {
                $tokens[] = ['type' => 'variable', 'name' => $matches[1], 'value' => $matches[2]];
            } elseif /* If statement */ (preg_match('/if\s+(.*)/', $line, $matches)) {
                // Extract the condition and transform variables for PHP
                $condition = $matches[1];
                // Replace variable names (assuming they don't start with a number and are alphanumeric + underscore)
                $condition = preg_replace_callback('/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/', function ($match) {
                    // Prefix variables with $
                    return '$' . $match[1];
                }, $condition);
                $tokens[] = ['type' => 'if', 'condition' => $condition];
            } elseif /* Else statement */ (preg_match('/else/', $line, $matches)) {
                $tokens[] = ['type' => 'else'];
            } elseif /* End statement */ (preg_match('/end/', $line, $matches)) {
                $tokens[] = ['type' => 'end'];
            } else {
                // Handle unrecognized lines
                echo "Error: Unrecognized line '$line'\n";
            }
        } elseif ($inPHP) {
            $tokens[] = ['type' => 'php', 'value' => $line];
        } elseif ($inHTML) {
            $tokens[] = ['type' => 'html', 'value' => $line];
        }
    }
    return $tokens;
}