<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
Route::get('v1/ping', fn () => response()->json(['pong' => true]));

Route::prefix('v1')
    ->middleware(['api'])
    ->group(function () {
        require base_path('app/Interfaces/Http/routes_v1.php');
    });
