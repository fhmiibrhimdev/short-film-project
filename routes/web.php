<?php

use App\Livewire\Example\Example;
use App\Livewire\Profile\Profile;
use App\Livewire\Dashboard\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Control\User as ControlUser;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Management\ShortFilm;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class);
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/short-film', ShortFilm::class);
    Route::get('/example', Example::class);
    Route::get('/control-user', ControlUser::class);
});

Route::group(['middleware' => ['auth', 'role:user']], function () {});
require __DIR__ . '/auth.php';
