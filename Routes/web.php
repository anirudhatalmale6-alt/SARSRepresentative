<?php

use Illuminate\Support\Facades\Route;

// Main listing
Route::get('/', 'SarsRepController@index')->name('index');

// Create wizard - Step 1: Entity & Representative details
Route::get('/create', 'SarsRepController@create')->name('create');
Route::post('/', 'SarsRepController@store')->name('store');

// Show request details
Route::get('/{id}', 'SarsRepController@show')->name('show');

// Edit request
Route::get('/{id}/edit', 'SarsRepController@edit')->name('edit');
Route::put('/{id}', 'SarsRepController@update')->name('update');

// Delete request
Route::delete('/{id}', 'SarsRepController@destroy')->name('destroy');

// Document upload (AJAX)
Route::post('/{id}/upload', 'SarsRepController@uploadDocument')->name('upload');

// Delete document (AJAX)
Route::delete('/{id}/document/{docId}', 'SarsRepController@deleteDocument')->name('deleteDocument');

// Generate documents (Mandate, Resolution, Cover Letter)
Route::post('/{id}/generate/{type}', 'SarsRepController@generateDocument')->name('generate');

// Generate final PDF bundle
Route::post('/{id}/generate-bundle', 'SarsRepController@generateBundle')->name('generateBundle');

// Download document
Route::get('/{id}/download/{docId}', 'SarsRepController@downloadDocument')->name('download');

// Update status (AJAX)
Route::put('/{id}/status', 'SarsRepController@updateStatus')->name('updateStatus');

// Audit log
Route::get('/{id}/audit', 'SarsRepController@audit')->name('audit');
