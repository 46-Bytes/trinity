<?php

use App\Http\Controllers\ChatController;
use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/diagnostic-status/{id}', function ($id) {
    $diagnostic = Diagnostic::find($id);
    // TODO: check if tasks are generated (ie. tasks.count>0)
    return response()->json([
        'adviceReady' => $diagnostic && $diagnostic->advice !== null,
    ]);
});


Route::post('/form/save-json', [FormController::class, 'saveJson']);
Route::post('/form/save-responses', [FormController::class, 'saveResponses']);
Route::get('/chat/messages', [ChatController::class, 'getMessages']);
