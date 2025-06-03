<?php

namespace App\View\Components\Flux;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    public function __construct(
        public bool $striped = true,
        public bool $hover = true,
        public bool $bordered = false,
        public bool $responsive = true,
        public ?string $id = null,
    ) {}

    public function render(): View
    {
        return view('components.flux.table');
    }
}
