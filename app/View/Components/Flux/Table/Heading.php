<?php

namespace App\View\Components\Flux\Table;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Heading extends Component
{
    public function __construct(
        public bool $sortable = false,
        public ?string $direction = null,
        public ?string $sortBy = null,
        public ?string $width = null,
    ) {}

    public function render(): View
    {
        return view('components.flux.table.heading');
    }
}
