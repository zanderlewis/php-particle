<?php

require 'utilities.php';

function formatParticleFile($filePath) {
    // Ensure the file exists before attempting to read
    if (!file_exists($filePath)) {
        echo "Error: File not found at '{$filePath}'\n";
        return;
    }

    // Read from the provided Particle file
    $content = file_get_contents($filePath);

    // Remove all newline characters
    $content = str_replace(["\r", "\n"], '', $content);

    // Apply formatting rules (this is a simplified example)
    $content = preg_replace('/let/', "\nlet", $content);
    $content = preg_replace('/print/', "\nprint", $content);
    $content = preg_replace('/if/', "\nif", $content);
    $content = preg_replace('/else/', "\nelse", $content);
    $content = preg_replace('/end/', "end\n", $content);
    $content = preg_replace('/match/', "\nmatch", $content);

    // Optionally, write the formatted content back to a file or output it
    echo $content;
}

// Check if a file path is provided as a command-line argument and if it ends with .prtcl
if ($argc < 2 || !endswith($argv[1], '.prtcl')) {
    echo "Usage: php accelerator.php <path_to_particle_file>.prtcl\n";
    exit(1);
}
$particleFilePath = $argv[1];
formatParticleFile($particleFilePath);