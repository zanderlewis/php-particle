<?php
$directory = '.'; // Replace with the actual directory path
$blockedFiles = ['index.php']; // Files to exclude from the list

// Get all PHP files in the directory
$phpFiles = glob($directory . '/*.php');

// Strip certain files from the list
$phpFiles = array_filter($phpFiles, function ($file) use ($blockedFiles) {
    return !in_array(basename($file), $blockedFiles);
});

// Generate HTML links for each PHP file
foreach ($phpFiles as $phpFile) {
    $fileName = basename($phpFile);
    $link = '<a style="font-size:4em;text-decoration:none;color:rgb(74,222,128);" href="' . $phpFile . '">' . $fileName . '</a>';
    echo $link . '<br>';
}
?>