<?php

/**
 * Fixes a common JSON error where string values contain unescaped literal newlines.
 *
 * This function uses a regular expression to find all JSON string literals
 * and then replaces any literal newlines (\r\n, \n, \r) within them with the
 * proper JSON escape sequence '\\n'.
 *
 * @param string $invalidJson The raw string data from the malformed JSON file.
 * @return string A syntactically correct JSON string that can be safely parsed.
 */
function fix_json_newlines(string $invalidJson): string
{
    // This regex finds all JSON string literals. It correctly handles escaped quotes (\") inside the strings.
    // Breakdown:
    // "         - Match the opening quote
    // (         - Start capturing group 1 (this will be the content of the string)
    //   (?:       - Start a non-capturing group
    //     \\.     - Match any escaped character (e.g., \", \\, \n)
    //     |       - OR
    //     [^"\\]  - Match any character that is NOT a quote or a backslash
    //   )*        - End non-capturing group, repeat it zero or more times
    // )         - End capturing group 1
    // "         - Match the closing quote
    // s         - "dotall" modifier, allows . to match newlines.
    $regex = '/"((?:\\\\.|[^"\\\\])*)"/s';

    return preg_replace_callback(
        $regex,
        function ($matches) {
            // $matches[0] is the full match, including the quotes (e.g., "\"Hello\nWorld\"")
            // $matches[1] is the raw content inside the quotes (e.g., "Hello\nWorld")
            
            // Get the content of the string (inside the quotes)
            $stringContent = $matches[1];
            
            // Replace all types of newlines (\r\n, \n, \r) with the JSON escape sequence '\\n'
            $fixedContent = str_replace(["\r\n", "\n", "\r"], '\\n', $stringContent);
            
            // Return the fixed content, re-wrapped in double quotes.
            return '"' . $fixedContent . '"';
        },
        $invalidJson
    );
}

// --- --- --- USAGE EXAMPLE --- --- ---

// 1. Get the content of your broken JSON file
// (Using a heredoc string here to simulate reading the file)
$brokenJsonString = file_get_contents("c:/Users/jstan/Desktop/STAGE-IWAN-annonces/fixingjson/broken.JSON");

echo "--- Original Broken JSON --- \n";
echo $brokenJsonString . "\n\n";

// 2. Try to decode the original string (this will fail)
$decoded_before = json_decode($brokenJsonString);
echo "--- Attempt to Decode Original --- \n";
var_dump($decoded_before); // This will output NULL
echo "JSON Error: " . json_last_error_msg() . "\n\n"; // Will show "Syntax error"

// 3. Fix the string using our function
$fixedJsonString = fix_json_newlines($brokenJsonString);

echo "--- Fixed JSON String --- \n";
echo $fixedJsonString . "\n\n";

// 4. Decode the fixed string (this will succeed)
$decoded_after = json_decode($fixedJsonString, true); // using true for an associative array
echo "--- Attempt to Decode Fixed String --- \n";
var_dump($decoded_after); // This will output the array successfully
echo "JSON Error: " . json_last_error_msg() . "\n"; // Will show "No error"

// The file path where you want to save the string
$filePath = "c:/Users/jstan/Desktop/STAGE-IWAN-annonces/fixingjson/better.JSON";

// Save the string to the file
if (file_put_contents($filePath, $fixedJsonString) !== false) {
    echo "File created and string saved successfully!";
} else {
    echo "Failed to save the string to the file.";
}

?>