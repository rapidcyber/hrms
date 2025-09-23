<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use PhpParser\Node\Expr\FuncCall;
use App\Http\Controllers\AttendanceController;

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
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/payroll', function(){
        return view('payroll.index');
    })->name('payroll');
    Route::get('/departments', function(){
        return view('departments.index');
    })->name('departments');
    Route::get('/positions', function(){
        return view('positions.index');
    })->name('positions');
    Route::get('/deductions', function(){
        return view('deductions.index');
    })->name('deductions');
    Route::get('/holidays', function(){
        return view('holidays.index');
    })->name('holidays');
    Route::get('/leaves', function(){
        return view('leaves.index');
    })->name('leaves');
    Route::get('/user-management', function(){
        return view('user-management');
    })->name('user-management');
    Route::get('/month13', function(){
        return view ('payroll.month13');
    })->name('month13');
});



require __DIR__.'/auth.php';
