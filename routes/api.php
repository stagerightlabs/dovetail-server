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

// Authenticated routes
Route::middleware(['auth:api', 'api'])->group(function () {

    // Current User's Profile
    Route::get('user', 'ProfileController@show')->name('user.show');
    Route::put('user', 'ProfileController@update')->name('user.update');
    Route::get('user/permissions/{permission}', 'CheckPermission')->name('user.permission');

    // Invitations
    Route::group(['namespace' => 'Invitations'], function () {
        Route::get('invitations', 'InvitationController@index')->name('invitations.index');
        Route::post('invitations', 'InvitationController@store')->name('invitations.store');
        Route::post('invitations/{hashid}/resend', 'ResendInvitation')->name('invitations.resend');
        Route::post('invitations/{hashid}/revoke', 'InvitationRevocationController@update')->name('invitations.revoke');
        Route::delete('invitations/{hashid}/revoke', 'InvitationRevocationController@delete')->name('invitations.restore');
        Route::delete('invitations/{hashid}', 'InvitationController@destroy')->name('invitations.destroy');
    });

    // Organization Members
    Route::group(['namespace' => 'Members'], function () {

        // Member Management
        Route::get('members', 'MembersController@index')->name('members.get');
        Route::get('members/deleted', 'DeletedMembersController@index')->name('members.deleted');
        Route::put('members/{hashid}', 'MembersController@update')->name('members.update');
        Route::delete('members/{hashid}', 'DeletedMembersController@store')->name('members.delete');
        Route::delete('members/{hashid}/restore', 'DeletedMembersController@destroy')->name('members.restore');

        // Member Permissions
        Route::get('members/{hashid}/permissions', 'PermissionsController@show')->name('permissions.show');
        Route::put('members/{hashid}/permissions', 'PermissionsController@update')->name('permissions.update');
    });

    // Organization
    Route::group(['namespace' => 'Organization'], function () {

        // The current organization
        Route::get('organization', 'OrganizationController@show')->name('organization');

        // Settings
        Route::get('organization/settings/{key}', 'SettingsController@show')->name('settings.show');
        Route::put('organization/settings', 'SettingsController@update')->name('settings.update');
    });

    // Logos
    Route::post('logos', 'LogoController@store')->name('logos.store');
    Route::delete('logos/{hashid}', 'LogoController@delete')->name('logos.delete');

    // Categories
    Route::get('categories', 'CategoryController@index')->name('categories.index');
    Route::post('categories', 'CategoryController@store')->name('categories.store');
    Route::get('categories/{hashid}', 'CategoryController@show')->name('categories.show');
    Route::put('categories/{hashid}', 'CategoryController@update')->name('categories.update');
    Route::delete('categories/{hashid}', 'CategoryController@delete')->name('categories.delete');

    // Teams
    Route::get('teams', 'TeamController@index')->name('teams.index');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('teams/{hashid}', 'TeamController@show')->name('teams.show');
    Route::put('teams/{hashid}', 'TeamController@update')->name('teams.update');
    Route::delete('teams/{hashid}', 'TeamController@delete')->name('teams.delete');

    // Team Membership
    Route::post('teams/{team}/members', 'TeamMembershipController@store')->name('teams.memberships.store');
    Route::delete('teams/{team}/members/{member}', 'TeamMembershipController@delete')->name('teams.memberships.delete');

    Route::group(['namespace' => 'Notebooks'], function () {

        // Notebooks
        Route::get('notebooks', 'NotebookController@index')->name('notebooks.index');
        Route::post('notebooks', 'NotebookController@store')->name('notebooks.store');
        Route::get('notebooks/{hashid}', 'NotebookController@show')->name('notebooks.show');
        Route::put('notebooks/{hashid}', 'NotebookController@update')->name('notebooks.update');
        Route::delete('notebooks/{hashid}', 'NotebookController@delete')->name('notebooks.delete');
        Route::post('notebooks/{hashid}/follow', 'NotebookFollowerController@store')->name('notebooks.follow');
        Route::delete('notebooks/{hashid}/unfollow', 'NotebookFollowerController@destroy')->name('notebooks.unfollow');

        // Page Order
        Route::put('notebooks/{hashid}/pages/sort-order', 'NotebookPageOrderController')->name('notebooks.sort-order');

        // Notebook Pages
        Route::get('notebooks/{hashid}/pages', 'PageController@index')->name('pages.index');
        Route::post('notebooks/{hashid}/pages', 'PageController@store')->name('pages.store');
        Route::get('notebooks/{notebook}/pages/{page}', 'PageController@show')->name('pages.show');
        Route::put('notebooks/{notebook}/pages/{page}', 'PageController@update')->name('pages.update');
        Route::delete('notebooks/{notebook}/pages/{page}', 'PageController@delete')->name('pages.delete');

        // Page Comments
        Route::get('notebooks/{notebook}/pages/{page}/comments', 'PageCommentController@index')->name('pages.comments.index');
        Route::post('notebooks/{notebook}/pages/{page}/comments', 'PageCommentController@store')->name('pages.comments.store');
        Route::get('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@show')->name('pages.comments.show');
        Route::put('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@update')->name('pages.comments.update');
        Route::delete('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@delete')->name('pages.comments.delete');
    });

    // Stripe Webhooks
    Route::post(
        'stripe/webhook',
        '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
    );
});
