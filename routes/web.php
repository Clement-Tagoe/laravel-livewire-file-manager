<?php

use App\Livewire\Pages\MyFiles;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::controller(FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {

});

Route::middleware(['auth'])->group(function () {

    Route::livewire('/my-files/{folder?}', MyFiles::class)
        ->where('folder', '(.*)')->name('my-files.index');
});


require __DIR__.'/settings.php';
