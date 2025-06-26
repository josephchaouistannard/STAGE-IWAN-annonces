<?php
// filepath: c:\Users\jstan\Desktop\STAGE-IWAN-annonces\fixingjson\test.php

/**
 * Attempts to fix a specific type of malformed JSON structure
 * where string values might contain unescaped newlines, double quotes,
 * backslashes, and other control characters.
 *
 * This is a heuristic based on observed patterns and standard JSON escaping rules
 * and may not fix all possible JSON errors or variations in the input.
 *
 * @param string $invalidJson The raw string data from the malformed JSON file.
 * @return string A string with attempted fixes applied. May still contain errors
 *                if the malformed patterns are different from expected.
 */
function fix_malformed_json(string $invalidJson): string
{
    $fixedJsonString = $invalidJson;

    // Remove potentially problematic structural fixes that might incorrectly
    // remove newlines that are part of a value string.
    // $fixedJsonString = str_replace("\n".'",', '",', $fixedJsonString);
    // $fixedJsonString = str_replace("\r\n".'",', '",', $fixedJsonString); // Handle Windows newlines

    // Find key-value pairs and fix the value string.
    // This regex attempts to match a quoted key, followed by a colon and optional whitespace,
    // then captures the raw value string (non-greedily), followed by a separator
    // (comma + optional whitespace + quote, or optional whitespace + closing brace/bracket).
    // The 's' modifier makes '.' match newlines.
    $fixedJsonString = preg_replace_callback(
        '/"([^"]+)"\s*:\s*(.*?)(,\s*"|\s*[\}\]])/s',
        function ($matches) {
            $key = $matches[1];
            $rawValue = $matches[2]; // This is the raw value string including its quotes, e.g., `"Value\nContent"` or `123` or `true`
            $separator = $matches[3];

            $fixedValue = $rawValue; // Default to original raw value

            // Check if the raw value is a quoted string (possibly with leading/trailing whitespace)
            // and extract its content.
            if (preg_match('/^\s*"(.*)"\s*$/s', $rawValue, $valueMatches)) {
                 $stringContent = $valueMatches[1]; // Content inside the quotes

                 // Apply comprehensive JSON string escaping rules to the captured content.
                 // Escape backslashes first to avoid double-escaping later.
                 $fixedContent = str_replace('\\', '\\\\', $stringContent);
                 // Escape double quotes.
                 $fixedContent = str_replace('"', '\\"', $fixedContent);
                 // Escape other standard JSON special characters.
                 $fixedContent = str_replace('/', '\\/', $fixedContent); // Optional, but common practice
                 $fixedContent = str_replace("\b", '\\b', $fixedContent); // Backspace
                 $fixedContent = str_replace("\f", '\\f', $fixedContent); // Form feed
                 $fixedContent = str_replace("\n", '\\n', $fixedContent); // Newline
                 $fixedContent = str_replace("\r", '\\r', $fixedContent); // Carriage return
                 $fixedContent = str_replace("\t", '\\t', $fixedContent); // Tab

                 // Note: This does not handle control characters U+0000 to U+001F
                 // other than \b, \f, \n, \r, \t, which would require \uXXXX escaping.
                 // Based on the error, newlines are the primary issue.

                 // Reconstruct the quoted string with the escaped content.
                 $fixedValue = '"' . $fixedContent . '"';
            }
            // If it's not a quoted string (number, boolean, null, object, array), we don't modify it.
            // This assumes non-string values are not malformed in this specific way.

            // Reconstruct the key-value pair with the fixed value and the original separator.
            return '"' . $key . '": ' . $fixedValue . $separator;
        },
        $fixedJsonString
    );


    // Optional Step: Attempt to fix potential trailing commas before the final closing brace/bracket.
    // This is a common JSON error. This is a basic attempt and might need refinement
    // depending on the exact structure, but it's unlikely to cause the reported newline error.
    $fixedJsonString = rtrim($fixedJsonString); // Remove trailing whitespace
    // Check if the string ends with a comma followed by optional whitespace and then a closing brace or bracket
    if (preg_match('/,\s*[\}\]]$/', $fixedJsonString)) {
         // Remove the trailing comma if it's just before the final closing brace or bracket
         $fixedJsonString = preg_replace('/,\s*([\}\]]$)/', '$1', $fixedJsonString);
    }


    return $fixedJsonString;
}

// --- --- --- USAGE EXAMPLE --- --- ---

// 1. Get the content of your broken JSON file
// Use the absolute path that was confirmed to work
$brokenJsonFilePath = "c:/Users/jstan/Desktop/STAGE-IWAN-annonces/fixingjson/broken.JSON";
$brokenJsonString = file_get_contents($brokenJsonFilePath);

if ($brokenJsonString === false) {
    die("Error: Could not read the file: " . $brokenJsonFilePath);
}

echo "--- Original Broken JSON (Snippet) --- \n";
// Only show a snippet of the original as it's large and broken
echo substr($brokenJsonString, 0, 500) . "...\n\n";


// 2. Try to decode the original string (this will fail)
$decoded_before = json_decode($brokenJsonString);
echo "--- Attempt to Decode Original --- \n";
var_dump($decoded_before); // This will output NULL
echo "JSON Error: " . json_last_error_msg() . "\n\n"; // Will show "Syntax error"

// 3. Fix the string using our function
$fixedJsonString = fix_malformed_json($brokenJsonString);

echo "--- Fixed JSON String (Snippet) --- \n";
// Only show a snippet of the fixed string
echo substr($fixedJsonString, 0, 500) . "...\n\n";


// 4. Decode the fixed string (this will hopefully succeed)
$decoded_after = json_decode($fixedJsonString, true); // using true for an associative array
echo "--- Attempt to Decode Fixed String --- \n";
var_dump($decoded_after); // This will output the array or NULL if still broken
echo "JSON Error: " . json_last_error_msg() . "\n"; // Will show "No error" if successful

// The file path where you want to save the string
$filePath = "c:/Users/jstan/Desktop/STAGE-IWAN-annonces/fixingjson/maybefixed.JSON";

// Save the string to the file
if (file_put_contents($filePath, $fixedJsonString) !== false) {
    echo "File created and string saved successfully!";
} else {
    echo "Failed to save the string to the file.";
}

?>