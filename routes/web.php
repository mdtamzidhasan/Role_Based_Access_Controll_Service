<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

// Roles
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
Route::get('/roles/{id}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions');
Route::post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
Route::post('/objects', [RoleController::class, 'storeObject'])->name('objects.store');

//User Role Assignment
Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
Route::post('/users/{userId}/role', [UserRoleController::class, 'assignRole'])->name('users.role.assign');
Route::delete('/users/{userId}/role', [UserRoleController::class, 'removeRole'])->name('users.role.remove');


// Default redirect 
Route::get('/', fn() => redirect()->route('roles.index'));
