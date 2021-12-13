<?php

namespace App\Service;

class Slugify
{

    public function generate(string $input): string
    {
        $characters = [' ', 'é', 'è', 'à', 'ç', 'ù'];
        $replace = ['-', 'e', 'e', 'a', 'c', 'u'];
        $output = str_replace($characters, $replace, $input);
        $output = preg_replace("[^a-zA-Z]", "", $output);
        $trimmed = trim($output);
        $output = preg_replace('#[ -]+#', '-', $trimmed);
        $output = strtolower($output);
        return $output;
    }

}