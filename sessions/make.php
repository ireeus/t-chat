<?php

// List of countries in Europe
$europeanCountries = [
    "Denmark", "Finland", "Iceland", "Norway", "Sweden",
    "Belgium", "France", "Luxembourg", "Netherlands",
    "Austria", "Czech Republic", "Germany", "Poland", "Slovakia", "Switzerland", "Liechtenstein",
    "Albania", "Bosnia and Herzegovina", "Croatia", "Greece", "Kosovo", "North Macedonia", "Montenegro", "Serbia", "Slovenia",
    "Belarus", "Bulgaria", "Moldova", "Poland", "Russia", "Romania", "Ukraine",
    "Cyprus", "Malta"
];

// Loop through the countries and create txt files
foreach ($europeanCountries as $country) {
    $fileName = str_replace(' ', '_', $country) . '_session.txt';
    $fileContent = "System: Welcome to $country session!
	";
    
    // Create or overwrite the file
    file_put_contents($fileName, $fileContent);
    
    // Output a message for each country
    echo "File '$fileName' created.\n";
}
?>
