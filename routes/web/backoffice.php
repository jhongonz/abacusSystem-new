<?php

use App\Http\Controllers\CampusController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(SecurityController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('authentication');
    Route::get('/home', 'home')->name('home');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(UserController::class)->prefix('user')->group(function () {
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
    Route::post('/set-logo', 'setLogoInstitution')->name('institution.set-logo');
    Route::post('/store', 'storeInstitution')->name('institution.store');
    Route::get('/delete/{id}', 'deleteInstitution')->whereNumber('id')->name('institution.delete-institution');
    Route::post('/set-state', 'changeStateInstitution')->name('institution.change-state-institution');
});

Route::controller(CampusController::class)->prefix('campus')->group(function () {
    Route::get('', 'index')->name('campus.index');
    Route::post('/get-list', 'getCampusCollection')->name('campus.get-campus-collection');
    Route::get('/get/{id?}', 'getCampus')->whereNumber('id')->name('campus.get-campus');
    Route::post('/store', 'storeCampus')->name('campus.store');
    Route::post('/set-state', 'changeStateCampus')->name('campus.change-state-campus');
    Route::get('/delete/{id?}', 'deleteCampus')->whereNumber('id')->name('campus.delete-campus');
});
