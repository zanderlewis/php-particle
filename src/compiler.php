<?php
// Compiler: Generate PHP code from tokens
function compile($tokens, $fileName)
{
    $phpCode = "<?php\n";
    foreach ($tokens as $token) {
        switch ($token['type']) {
            case 'php_start':
                // Remove the opening tag

                break;
            case 'endphp':
                // Remove the closing tag

                break;
            case 'php':
                $phpCode .= $token['value'] . "\n";
                break;
            case 'print':
                // Check if the value is a variable by looking for a leading $
                if (preg_match('/^\$([a-zA-Z_][a-zA-Z0-9_]*)$/', $token['value'], $varMatches)) {
                    // If it's a variable, print its value
                    $phpCode .= "echo " . $token['value'] . ";\n";
                    $phpCode .= "echo \"<br>\";\n";
                } else {
                    // Otherwise, treat it as a string literal
                    $value = addslashes($token['value']);
                    $phpCode .= "echo \"$value\";\n";
                    $phpCode .= "echo \"<br>\";\n";
                }
                break;
            case 'match':
                $pattern = addslashes($token['pattern']);
                $string = addslashes($token['string']);
                $phpCode .= "\$matchResult = preg_match('/$pattern/', \"$string\");\n";
                if (isset($token['as'])) {
                    $phpCode .= "\$" . $token['as'] . " = \$matchResult ? 'True' : 'False';\n";
                    break;
                }
                $phpCode .= "echo \$matchResult ? 'True' : 'False';\n";
                break;
            case 'comment':
                // Strip of space right after //
                $token['value'] = ltrim($token['value']);
                // Simply add a comment to the generated PHP code
                $phpCode .= "// " . $token['value'] . "\n";
                break;
            case 'variable':
                // Assign the variable value to a PHP variable
                $phpCode .= "\$" . $token['name'] . " = " . $token['value'] . ";\n";
                break;
            case 'if':
                // Add an if statement with the condition
                $phpCode .= "if (" . $token['condition'] . ") {\n";
                break;
            case 'else':
                // Add an else statement
                $phpCode .= "} else {\n";
                break;
            case 'end':
                // Add an end statement
                $phpCode .= "}\n";
                break;
        }
    }
    // Write to php file
    file_put_contents($fileName . '.php', $phpCode);
}