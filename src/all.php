<?php

require 'utilities.php';
require 'parser.php';
require 'compiler.php';

// Check if a file path is provided as a command-line argument and if it ends with .particle
if ($argc < 2 || !endswith($argv[1], '.prtcl')) {
    echo "Usage: php syntax.php <path_to_particle_file>.prtcl\n";
    exit(1);
}

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

// Assuming parse() and compile() functions are defined earlier in the script
$tokens = parse($userCode);
compile($tokens, $particleFileName); // This function should already generate and write the PHP code to 'output.php'
