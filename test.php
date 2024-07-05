<?php

// Define the output file
$outputFile = 'wrk_results.json';

// Define the command
$command = "wrk -t4 -c100 -d30s -s wrk_script.lua http://localhost:9501/users | tee $outputFile";

// Execute the command
$output = shell_exec($command);

// Check if the command executed successfully
if ($output === null) {
    echo "Error executing the command. Make sure wrk is installed and the script path is correct.\n";
    exit(1);
}

// Extract JSON part from the output
if (preg_match('/(\{.*\})/s', $output, $matches)) {
    $jsonOutput = $matches[1];
} else {
    echo "No JSON data found in the output.\n";
    exit(1);
}

// Parse the JSON output
$result = json_decode($jsonOutput, true);

// Check if JSON parsing was successful
if ($result === null) {
    echo "Error parsing JSON output. Here's the extracted JSON:\n";
    echo $jsonOutput;
    exit(1);
}

// Now you can use $result as a PHP array
print_r($result);

// Save only the JSON part to the file
file_put_contents($outputFile, $jsonOutput);

echo "\nJSON results have been saved to $outputFile\n";

?>