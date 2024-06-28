<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoletoController;

Route::post('/upload/boletos', [BoletoController::class, 'handleBoletos']);
