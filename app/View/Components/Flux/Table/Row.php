<?php

namespace App\View\Components\Flux\Table;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Row extends Component
{

    public bool $hover = true;
    public function __construct(
        public bool $even = false,
        $hover = true
    ) {

        $this->hover = $hover;
    }

    public function render(): View
    {
        return view('components.flux.table.row');
    }
}
