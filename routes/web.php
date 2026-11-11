<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
| "/" renders resources/views/home.blade.php.
| Route::view() is a shortcut for a route that only returns a view.
| (In Step 7 this becomes a controller that loads content from the database.)
| ->name('home') lets the code link to it with route('home') instead of "/".
*/
Route::view('/', 'home')->name('home');
