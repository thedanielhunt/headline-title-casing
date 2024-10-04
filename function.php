<?php

function headlineTitleCase($string) {
	
//Derived from New York Times and (old) Los Angeles Times headline style
//Future considerations would be to capitalize "up," "yet" and other 
// words when they are used in conjunctions like "Grow Up" or "Take Out"
//Future consideration would be to capitalize propositions that become 
// adverbs: Drops In, Drones Out, Stands By, etc.
//Idioms should remain lowercased: Call on, Call for
//When proposition takes the place of a verb, it should be capitalized as well
//Double-vowed words like Co-op should be lowercased
//Perfect style would be to additionally capitalize all nouns, pronouns 
// and verbs, and all other words of four or more letters
	
    // List of exceptions that should not be capitalized
    $exceptions = [
        'a', 'an', 'and', 'as', 'at', 'but', 'by', 'for', 'if', 'in', 'nor',
        'of', 'on', 'or', 'out', 'so', 'the', 'to', 'up', 'v.', 'vs.', 'via', 'yet'
    ];

    // Dollar amount pattern to identify words like $7 million, $53 million, etc.
    $dollarAmountPattern = '/\$\d+(\.\d+)?\s+(million|billion|thousand|trillion)/i';

    // Split the string into words
    $words = preg_split('/\s+/', $string);
    $totalWords = count($words);

    // Capitalize the first and last words, regardless of exceptions
    $words[0] = ucfirst(strtolower($words[0]));
    if ($totalWords > 1) {
        $words[$totalWords - 1] = ucfirst(strtolower($words[$totalWords - 1]));
    }

    // Loop through each word and apply capitalization rules
    for ($i = 1; $i < $totalWords - 1; $i++) {
        $lowerWord = strtolower($words[$i]);
		
        // Handle hyphenated words: Capitalize the second part if it has 4 or more letters
        if (strpos($lowerWord, '-') !== false && strpos($lowerWord, '/') !== false) {
            $hyphenatedParts = explode('-', $lowerWord);
            if (strlen($hyphenatedParts[1]) >= 4) {
                $hyphenatedParts[1] = ucfirst($hyphenatedParts[1]);
            }
            $words[$i] = implode('-', array_map('ucfirst', $hyphenatedParts));
        }		

        // Capitalize words if they are not in the exceptions list
        if (!in_array($lowerWord, $exceptions)) {
            $words[$i] = ucfirst($lowerWord);
        } else {
            $words[$i] = $lowerWord;
        }

        // Always capitalize if the previous character was a colon or em dash
        if (substr($words[$i - 1], -1) === ':' || substr($words[$i - 1], -2) === '—') {
            $words[$i] = ucfirst($lowerWord);
        }
    }

    // Recheck each word to handle dollar amount patterns
    foreach ($words as &$word) {
        // Capitalize "million," "billion," and "thousand" when they follow a dollar amount
        if (preg_match($dollarAmountPattern, $word)) {
            $word = preg_replace_callback('/\b(million|billion|thousand)\b/i', function ($matches) {
                return ucfirst($matches[0]);
            }, $word);
        }
    }
	
    // Join the words back into a string and return
    return implode(' ', $words);
}
