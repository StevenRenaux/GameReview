<?php

namespace App\Services;

use Symfony\Component\String\Slugger\AsciiSlugger;

class Slugger
{
    public function toSlug($gameTitle)
    {
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($gameTitle, '-');
        return $slug;
    }
}
