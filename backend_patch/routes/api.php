<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin full access
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('classes', SchoolClassController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('routines', RoutineController::class);
        Route::apiResource('notices', NoticeController::class);

        Route::post('attendance/mark', [AttendanceController::class, 'mark']);
        Route::get('attendance/list', [AttendanceController::class, 'list']);
        Route::get('attendance/reports', [AttendanceController::class, 'reports']);
    });

    // Teacher limited
    Route::middleware('role:teacher')->group(function () {
        Route::get('students', [StudentController::class, 'index']);
        Route::get('students/{student}', [StudentController::class, 'show']);

        Route::get('classes', [SchoolClassController::class, 'index']);
        Route::get('sections', [SectionController::class, 'index']);
        Route::get('subjects', [SubjectController::class, 'index']);
        Route::get('routines', [RoutineController::class, 'index']);
        Route::get('notices', [NoticeController::class, 'index']);

        Route::post('attendance/mark', [AttendanceController::class, 'mark']);
        Route::get('attendance/list', [AttendanceController::class, 'list']);
    });
});
