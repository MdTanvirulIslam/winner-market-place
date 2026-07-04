<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class StoreLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $metaDescription = null,
        public ?string $ogImage = null,
    ) {
    }

    public function render(): View
    {
        return view('layouts.store');
    }
}
