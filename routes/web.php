<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/news/category/{category:slug}', [CategoryController::class, 'show'])->name('news.category');
Route::get('/news/tag/{tag:slug}', [TagController::class, 'show'])->name('news.tag');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
