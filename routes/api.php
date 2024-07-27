<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

Route::get('/pokemons', [PokemonController::class, 'index']);   
Route::post('/find', [PokemonController::class, 'find']);
Route::post('/catch', [PokemonController::class, 'catch']);
Route::post('/rename/{id}', [PokemonController::class, 'rename']);
Route::post('/release/{id}', [PokemonController::class, 'release']);
