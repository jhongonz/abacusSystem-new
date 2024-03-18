<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::name('panel.')->group(base_path('routes/web/backoffice.php'));

Route::get('/', function(){
    if (Auth::check()) {
        return redirect()->route('panel.home');
    }
    return redirect()->route('panel.login');
})->middleware(['guest'])->name('index');
