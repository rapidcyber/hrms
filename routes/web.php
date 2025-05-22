<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use PhpParser\Node\Expr\FuncCall;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Route::get('/employees', function(){
        return view('employees.index');
    })->name('employees');
    Route::get('/attendance', function(){
        return view('attendance.index');
    })->name('attendance');
    Route::get('/payroll', function(){
        return view('payroll.index');
    })->name('payroll');
});



require __DIR__.'/auth.php';
