<?php

namespace App\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeComposer
{
    public function compose(View $view): void
    {
        $random = Str::random(10);
        $view->with('versionRandom', $random);
    }
}
