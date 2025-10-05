<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\DashboardManager;
use App\Http\Controllers\EditorManager;

use App\Http\Controllers\MindmapManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/login',[AuthManager::class,'showLoginForm'])->name('loginForm');
Route::post('/login',[AuthManager::class,'login'])->name('login');
Route::get('/register',[AuthManager::class,'showRegisterForm'])->name('registerForm');
Route::post('/register',[AuthManager::class,'register'])->name('register');


Route::group(['middleware' => 'auth'], function () {


Route::get('/dashboard', [DashboardManager::class, 'index'])->name('dashboard');
Route::get('/editor/{id}',[EditorManager::class,'edit'])->name('script.editor');
Route::get('/script/create', [EditorManager::class, 'index'])->name('script.create');
Route::post('/script/create', [EditorManager::class, 'create'])->name('script.store');
Route::post('/editor/save', [EditorManager::class, 'save'])->name('script.save');
//Route::get('/analyse-script/{id}', [AnalysisController::class, 'analyze'])->name('script.analyse');
Route::get('/viewmindmap/{id}', [MindmapManager::class, 'viewMindmap'])->name("viewMindmap");
    Route::get('/analyse-script/{script_id}', [AnalysisController::class, 'showAnalysis'])
        ->name('analysis.show');

Route::post('/analyse-script', [AnalysisController::class, 'fetchAnalysis'])
        ->name('api.analysis.fetch');
Route::post('/regenerate-mindmap', [MindmapManager::class, 'generateChrRelationMmRegen'])->name("regenCharMindMap");
Route::get('/logout', [AuthManager::class, 'logout'])->name('logout');
});
