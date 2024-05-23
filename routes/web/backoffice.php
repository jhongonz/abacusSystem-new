<?php

use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('authentication');
    Route::get('/home', 'home')->name('home');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(UserController::class)->prefix('user')->group(function () {
    //    Route::get('','index')->name('user.index');
    //    Route::post('/get-list','getUsers')->name('user.get-users');
    //    Route::post('/set-state','changeStateUser')->name('user.change-state-user');
    //    Route::get('/delete/{id}','deleteUser')->whereNumber('id')->name('user.delete-user');
    //    Route::get('/get/{id?}','getUser')->whereNumber('id')->name('user.get-user');
    //    Route::post('/store','storeUser')->name('user.store');
    Route::get('/recovery-account', 'recoveryAccount')->name('user.recovery');
    Route::post('/validate-account', 'validateAccount')->name('user.validate-account');
    Route::get('/reset-account/{id}', 'resetAccount')->whereNumber('id')->name('user.reset-account');
    Route::post('/reset-password', 'resetPassword')->name('user.reset-password');
});

Route::controller(EmployeeController::class)->prefix('employees')->group(function () {
    Route::get('', 'index')->name('employee.index');
    Route::post('/get-list', 'getEmployees')->name('employee.get-employees');
    Route::post('/set-state', 'changeStateEmployee')->name('employee.change-state-employee');
    Route::get('/edit/{id?}', 'getEmployee')->whereNumber('id')->name('employee.get-employee');
    Route::post('/set-image', 'setImageEmployee')->name('employee.set-image');
    Route::post('/store', 'storeEmployee')->name('employee.store');
    Route::get('/delete/{id}', 'deleteEmployee')->whereNumber('id')->name('employee.delete-employee');
});

Route::controller(ModuleController::class)->prefix('modules')->group(function () {
    Route::get('', 'index')->name('module.index');
    Route::post('/get-list', 'getModules')->name('module.get-modules');
    Route::post('/set-state', 'changeStateModule')->name('module.change-state-module');
    Route::get('/delete/{id}', 'deleteModule')->name('module.delete-module');
    Route::get('/get/{id?}', 'getModule')->whereNumber('id')->name('module.get-module');
    Route::post('/store', 'storeModule')->name('module.store');
});

Route::controller(ProfileController::class)->prefix('profiles')->group(function () {
    Route::get('', 'index')->name('profile.index');
    Route::post('/get-list', 'getProfiles')->name('profile.get-profiles');
    Route::post('/set-state', 'changeStateProfile')->name('profile.change-state-profile');
    Route::get('/delete/{id}', 'deleteProfile')->whereNumber('id')->name('profile.delete-profile');
    Route::get('/get/{id?}', 'getProfile')->whereNumber('id')->name('profile.get-profile');
    Route::post('/store', 'storeProfile')->name('profile.store');
});

Route::controller(InstitutionController::class)->prefix('institution')->group(function () {
    Route::get('', 'index')->name('institution.index');
    Route::post('/get-list', 'getInstitutions')->name('institution.get-institutions');
    Route::get('/get/{id?}', 'getInstitution')->whereNumber('id')->name('institution.get-institution');
});
