<?php

use App\Http\Controllers\Api\PermissionCheckController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserRoleController;
use Illuminate\Support\Facades\Route;

// ── Roles ─────────────────────────────────────────────────
Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
Route::get('/roles/{id}/permissions', [RoleController::class, 'permissions']);
Route::put('/roles/{id}/permissions', [RoleController::class, 'updatePermissions']);

// ── User Role Assignment ───────────────────────────────────
Route::get('/users', [UserRoleController::class, 'index']);
Route::put('/users/{userId}/role', [UserRoleController::class, 'assignRole']);
Route::delete('/users/{userId}/role', [UserRoleController::class, 'removeRole']);

// ── Permission Check (EMS এই endpoints call করবে) ─────────
Route::get('/check/{userId}', [PermissionCheckController::class, 'check']);
Route::post('/check/{userId}/has', [PermissionCheckController::class, 'has']);