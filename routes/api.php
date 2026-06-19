<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile/me', [AuthController::class, 'me']);
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/chat/rooms', [ChatController::class, 'getRooms']);
    Route::get('/chat/rooms/{roomId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/rooms/{roomId}/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/rooms/{roomId}/read', [ChatController::class, 'markAsRead']);
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::post('/chat/rooms', [ChatController::class, 'createRoom']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
    Route::delete('/chat/rooms/{id}', [ChatController::class, 'deleteRoom']);
    Route::delete('/chat/rooms/{id}/clear', [ChatController::class, 'clearChat']);
    Route::get('/chat/rooms/{id}/info', [ChatController::class, 'roomInfo']);
});
