<?php

use App\Http\Controllers\Api\JobController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/jobs', [JobController::class, 'index'])->name('home');
