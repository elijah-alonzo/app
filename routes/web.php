<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route - redirects based on guard
Route::get('/dashboard', function () {
    // If student is logged in, redirect to Filament student panel
    if (Auth::guard('student')->check()) {
        return redirect('/student');
    }

    // If admin is logged in, redirect to Filament admin panel
    if (Auth::guard('web')->check()) {
        return redirect('/admin');
    }

    // If no one is logged in, redirect to welcome
    return redirect('/');
})->middleware(['auth:web,student'])->name('dashboard');
