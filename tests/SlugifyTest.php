<?php

namespace App\Service;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class SlugifyTest extends TestCase
{
    public function testGenerate(): void
    {
        $slugify = new Slugify(); 
        
        assertEquals('games-of-thrones', $slugify->generate('games of thrones'));
        assertEquals('anton-bank-linder', $slugify->generate(' anton - bank linder  '));
        assertEquals('metallica-hero-of-the-day', $slugify->generate('Metallica - Hero of the day'));
    }
}