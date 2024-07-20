<?php

require 'utilities.php';
require 'parser.php';
require 'compiler.php';

// Check if a file path is provided as a command-line argument and if it ends with .particle
if ($argc < 2 || !endswith($argv[1], '.prtcl')) {
    echo "Usage: php syntax.php <path_to_particle_file>.prtcl\n";
    exit(1);
}

// Check for flags
$flags = [];
for ($i = 2; $i < $argc; $i++) {
    if (startswith($argv[$i], '-')) {
        $flags[] = substr($argv[$i], 1);
    }
}

// -m flag: Mass compile all .prtcl files in the directory
if (in_array('m', $flags)) {
    $directory = dirname($argv[1]);
    $files = glob($directory . '/*.prtcl');
    foreach ($files as $file) {
        $userCode = file_get_contents($file);
        $particleFileName = pathinfo($file, PATHINFO_FILENAME);
        $tokens = parse($userCode);
        compile($tokens, $particleFileName);
    }
    exit(0);
} else {
    // Use the first command-line argument as the .particle file path
    $particleFilePath = $argv[1];

    // Ensure the file exists before attempting to read
    if (!file_exists($particleFilePath)) {
        echo "Error: File not found at '{$particleFilePath}'\n";
        exit(1);
    }

    // Read from the provided .particle file
    $userCode = file_get_contents($particleFilePath);

    // Get the filename
    $particleFileName = pathinfo($particleFilePath, PATHINFO_FILENAME);

    $tokens = parse($userCode);
    compile($tokens, $particleFileName);
}
