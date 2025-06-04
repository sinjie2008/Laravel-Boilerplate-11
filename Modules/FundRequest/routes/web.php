<?php

use Illuminate\Support\Facades\Route;
use Modules\FundRequest\App\Http\Controllers\FundRequestController;

Route::group(['middleware' => ['auth', 'web'], 'prefix' => 'admin'], function () {
    Route::resource('fund-requests', FundRequestController::class)
        ->names('fundrequest.fundrequests')
        ->middleware('permission:view fund requests|create fund requests|update fund requests|delete fund requests');
});
