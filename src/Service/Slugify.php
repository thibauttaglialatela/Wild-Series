<?php

namespace App\Service;

class Slugify
{

    public function generate(string $input): string
    {
        //change é, è, à, ù, ç par e, a, u, c
        $output = htmlentities($input, ENT_COMPAT, "UTF-8");
        $output = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|ring);/','$1',$output);
        $output = html_entity_decode($output);
        //enlever les caractéres en début et fin de chaine
        $output = trim($input);
        //enléve les - multiples
        $output = preg_replace('#[ -]+#', '-', $output);
        // lowercase
        $output = strtolower($output);
        return $output;
    }

}