<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $output = strip_tags($input);
        $output = preg_replace('~[^\pL\d]+~u', '-', $output);
        setlocale(LC_ALL, 'en_US.utf8');
        $output = iconv('utf-8', 'us-ascii//TRANSLIT', $output);
        $output = preg_replace('~[^-\w]+~', '', $output);
        $output = trim($output, '-');
        $output = preg_replace('~-+~', '-', $output);
        $output = strtolower($output);
        if (empty($output)) {
            return 'n-a';
        }
        return $output;
    }

}
