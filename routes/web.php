<?php

use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Frontend\AgendaController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/agenda', [AgendaController::class, 'index'])->name('agendas.index');

Route::get('/siswa/ujian', [ExamController::class, 'show'])->name('student.exam.show');
Route::post('/siswa/ujian', [ExamController::class, 'submit'])->name('student.exam.submit');
