<?php

use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\DepensesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/inscription', [
    InscriptionController::class,
    'create',
])->name('register');

Route::post('/inscription', [
    InscriptionController::class,
    'store',
])->name('register.store');

Route::get('/connexion', [
    ConnexionController::class,
    'create',
])->name('login');

Route::post('/connexion', [
    ConnexionController::class,
    'store',
])->middleware('throttle:5,1')->name('login.store');

Route::post('/deconnexion', [
    ConnexionController::class,
    'destroy',
])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    //----------------Catégories ----------------------
    Route::get('/categories/creer', [
        CategorieController::class,
        'create',
    ])->name('categories.create');

    Route::post('/categories', [
        CategorieController::class,
        'store',
    ])->name('categories.store');

    Route::get('/categories', [
        CategorieController::class,
        'index',
    ])->name('categories.index');
    Route::get('/categories/{cat}', [
        CategorieController::class,
        'show'
    ])->name('categorie.show');
    Route::get('/categories/{cat}/modifier', [
        CategorieController::class,
        'edit'
    ])->name('categories.edit');
    Route::patch('categorie/{cat}', [
        CategorieController::class,
        'update'
    ])->name('categories.update');
    Route::delete('categories/{cat}', [
        CategorieController::class,
        'delete'
    ])->name('categories.delete');

    //------------------- Depenses -------------------

    Route::get('/depenses/creer',[
        DepensesController::class,
        'create'
    ])->name('depenses.create');
    Route::post('/depenses', [
        DepensesController::class,
        'store',
    ])->name('depenses.store');
    Route::get('/depenses', [
        DepensesController::class,
        'index'
    ])->name('depenses.index');
    Route::get('/depenses/{dep}', [
        DepensesController::class,
        'show'
    ])->name('depenses.show');
    Route::get('/depenses/{dep}/modifier', [
        DepensesController::class,
        'edit'
    ])->name('depenses.edit');
    Route::patch('/depenses/{dep}', [
        DepensesController::class,
        'update'
    ])->name('depenses.update');
    Route::delete('depenses/{dep}', [
        DepensesController::class,
        'delete'
    ])->name('depenses.delete');

});
