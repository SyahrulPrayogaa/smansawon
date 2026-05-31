<?php

use App\Http\Controllers\Admin\ScoreRecapExportController;
use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Frontend\AgendaController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return redirect()->route('student.exam.login');
})->name('home');

// Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
// Route::get('/berita/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Route::get('/agenda', [AgendaController::class, 'index'])->name('agendas.index');

Route::prefix('ujian')->name('student.exam.')->group(function () {
    Route::get('/', [ExamController::class, 'login'])->name('login');
    Route::post('/cek-nisn', [ExamController::class, 'checkNisn'])->name('check-nisn');

    Route::get('/profil', [ExamController::class, 'profile'])->name('profile');
    Route::post('/cek-token', [ExamController::class, 'checkToken'])->name('check-token');

    Route::get('/soal/{number}', [ExamController::class, 'question'])->name('question');
    Route::post('/soal/{number}', [ExamController::class, 'saveAnswer'])->name('save-answer');

    Route::post('/selesai', [ExamController::class, 'finish'])->name('finish');
    Route::get('/hasil', [ExamController::class, 'result'])->name('result');

    Route::get('/terkunci', [ExamController::class, 'locked'])->name('locked');
    Route::post('/catat-pelanggaran', [ExamController::class, 'recordViolation'])->name('record-violation');

    Route::post('/keluar', [ExamController::class, 'logout'])->name('logout');
});

Route::get('/admin/score-recap/export-pdf', [ScoreRecapExportController::class, 'pdf'])
    ->middleware(['web', 'auth'])
    ->name('admin.score-recap.pdf');
