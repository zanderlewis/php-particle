<?php
// Compiler: Generate PHP code from tokens
function compile($tokens, $fileName)
{
    $phpCode = "<?php\n";
    $inHtmlBlock = false;
    $htmlContent = '';
    foreach ($tokens as $token) {
        switch ($token['type']) {
            case 'php':
                if ($inHtmlBlock) {
                    $htmlContent .= "<?php " . $token['value'] . " ?>\n";
                } else {
                    $phpCode .= $token['value'] . "\n";
                }
                break;
            case 'html_start':
                $inHtmlBlock = true;
                $htmlContent = '';
                break;
            case 'html_end':
                $inHtmlBlock = false;
                // Process the HTML content for templating
                $processedHtmlContent = preg_replace_callback('/\{\{(.+?)\}\}/', function ($matches) {
                    // Correctly concatenate the PHP variable
                    return htmlspecialchars($matches[1]);
                }, $htmlContent);
                $phpCode .= "echo \"" . addslashes($processedHtmlContent) . "\";\n";
                $htmlContent = '';
                break;
            case 'html':
                if ($inHtmlBlock) {
                    $htmlContent .= $token['value'] . "\n";
                } else {
                    $phpCode .= "echo \"" . addslashes($token['value']) . "\";\n";
                }
                break;
            case 'import':
                // Import the script into the script that is importing it.
                $importedFileName = $token['path'];
                $importedCode = file_get_contents($importedFileName);
                $importedTokens = parse($importedCode);
                compile($importedTokens, $importedFileName);
                $phpCode .= "require '$importedFileName.php';\n";
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
            case 'elif':
                // Add an else if statement with the condition
                $phpCode .= "} elseif (" . $token['condition'] . ") {\n";
                break;
            case 'else':
                // Add an else statement
                $phpCode .= "} else {\n";
                break;
            case 'end':
                // Add an end statement to close the block
                $phpCode .= "}\n";
                break;
        }
    }
    // Create particle directory if it doesn't exist
    if (!file_exists('particle')) {
        mkdir('particle');
    }
    // Write to php file
    file_put_contents('particle/' . $fileName . '.php', $phpCode);
}
