<?php

use App\Livewire\Month13;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Month13::class)
        ->assertStatus(200);
});
