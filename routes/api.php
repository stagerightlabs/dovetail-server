<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes...
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::post('register', 'Auth\RegisterController@register')->name('register');

// Invitation Redemption
Route::get('invitations/{code}/confirm', 'Invitations\Confirm')->name('invitations.confirm');
Route::post('invitations/{code}/redeem', 'Invitations\Redeem')->name('invitations.redeem');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email Verification Routes...
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');


Route::middleware(['auth:api', 'api'])->group(function () {

    // Current User
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    // Current User's Organization
    Route::get('/organization', function (Request $request) {
        return $request->organization;
    })->name('organization');


    // Invitations
    Route::group(['namespace' => 'Invitations'], function () {
        Route::get('invitations', 'InvitationController@index')->name('invitations.index');
        Route::post('invitations', 'InvitationController@store')->name('invitations.store');
        Route::post('invitations/{hashid}/resend', 'ResendInvitation')->name('invitations.resend');
        Route::post('invitations/{hashid}/revoke', 'InvitationRevocationController@update')->name('invitations.revoke');
        Route::delete('invitations/{hashid}/revoke', 'InvitationRevocationController@delete')->name('invitations.restore');
        Route::delete('invitations/{hashid}', 'InvitationController@destroy')->name('invitations.destroy');
    });
});
