<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    ray('Debugging works, hooray!');

    return view('welcome');
});
