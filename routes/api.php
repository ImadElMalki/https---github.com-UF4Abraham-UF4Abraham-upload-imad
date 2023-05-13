<?php

use App\Http\Controllers\CameresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::apiResource('cameres_seguretat', CameresController::class);

Route::get('location/{lat}/{lon}', [CameresController::class, 'location'])->name('cameres_seguretat.location');

Route::post('filter', [CameresController::class, 'filter'])->name('cameres_seguretat.store');

Route::get('ubicacio_exacta/{id_cam_seguretat}', [CameresController::class, 'ubicacio_exacta'])->name('cameres_seguretat.ubicacio_exacta');

Route::get('help', [CameresController::class, 'help'])->name('cameres_seguretat.help');