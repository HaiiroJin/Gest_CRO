<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return redirect('/admin');
});
