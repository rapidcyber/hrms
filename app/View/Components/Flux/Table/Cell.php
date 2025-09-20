<?php

namespace App\View\Components\Flux\Table;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Cell extends Component
{
    public function __construct(
        public ?string $colspan = null,
        public ?string $rowspan = null,
    ) {}

    public function render(): View
    {
        return view('components.flux.table.cell');
    }
}
