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
Route::post('register', 'Auth\RegistrationController@store')->name('register');

// Invitation Redemption
Route::get('invitations/{code}/confirm', 'Invitations\InvitationConfirmationController@create')->name('invitations.confirm');
Route::post('invitations/{code}/redeem', 'Invitations\InvitationRedemptionController@store')->name('invitations.redeem');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email Verification Routes...
Route::get('email/verify/{code}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// Authenticated routes
Route::middleware(['auth:api', 'api'])->group(function () {

    // Current User's Profile
    Route::get('user', 'ProfileController@show')->name('user.show');
    Route::put('user', 'ProfileController@update')->name('user.update');
    Route::get('user/permissions/{permission}', 'User\PermissionCheckController@show')->name('user.permission');
    Route::get('user/notifications', 'NotificationController@index')->name('user.notifications.index');
    Route::get('user/notifications/{uuid}', 'NotificationController@show')->name('user.notifications.show');
    Route::put('user/notifications/{uuid}', 'NotificationController@update')->name('user.notifications.update');
    Route::get('user/admin', 'User\AdminStatusController@show')->name('user.flags.admin');
    Route::get('user/readonly', 'User\ReadOnlyStatusController@show')->name('user.flags.readonly');
    Route::get('user/teams', 'User\UserTeamController@show')->name('user.teams');
    Route::get('user/notebooks', 'User\UserNotebookController@show')->name('user.notebooks');

    // Invitations
    Route::group(['namespace' => 'Invitations'], function () {
        Route::get('invitations', 'InvitationController@index')->name('invitations.index');
        Route::post('invitations', 'InvitationController@store')->name('invitations.store');
        Route::post('invitations/{hashid}/resend', 'ResendInvitationController@store')->name('invitations.resend');
        Route::post('invitations/{hashid}/revoke', 'InvitationRevocationController@update')->name('invitations.revoke');
        Route::delete('invitations/{hashid}/revoke', 'InvitationRevocationController@destroy')->name('invitations.restore');
        Route::delete('invitations/{hashid}', 'InvitationController@destroy')->name('invitations.destroy');
    });

    // Organization Members
    Route::group(['namespace' => 'Members'], function () {

        // Member Management
        Route::get('members', 'MembersController@index')->name('members.get');
        Route::get('members/deleted', 'DeletedMembersController@index')->name('members.deleted');
        Route::put('members/{hashid}', 'MembersController@update')->name('members.update');
        Route::delete('members/{hashid}/block', 'BlockedMembersController@store')->name('members.block');
        Route::delete('members/{hashid}/unblock', 'BlockedMembersController@destroy')->name('members.unblock');
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
    Route::delete('logos/{hashid}', 'LogoController@destroy')->name('logos.destroy');

    // Categories
    Route::get('categories', 'CategoryController@index')->name('categories.index');
    Route::post('categories', 'CategoryController@store')->name('categories.store');
    Route::get('categories/{hashid}', 'CategoryController@show')->name('categories.show');
    Route::put('categories/{hashid}', 'CategoryController@update')->name('categories.update');
    Route::delete('categories/{hashid}', 'CategoryController@destroy')->name('categories.destroy');

    // Teams
    Route::get('teams', 'TeamController@index')->name('teams.index');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('teams/{hashid}', 'TeamController@show')->name('teams.show');
    Route::put('teams/{hashid}', 'TeamController@update')->name('teams.update');
    Route::delete('teams/{hashid}', 'TeamController@destroy')->name('teams.destroy');

    // Team Membership
    Route::post('teams/{team}/members', 'TeamMembershipController@store')->name('teams.memberships.store');
    Route::delete('teams/{team}/members/{member}', 'TeamMembershipController@destroy')->name('teams.memberships.destroy');

    Route::group(['namespace' => 'Notebooks'], function () {

        // Notebooks
        Route::get('notebooks', 'NotebookController@index')->name('notebooks.index');
        Route::post('notebooks', 'NotebookController@store')->name('notebooks.store');
        Route::get('notebooks/{hashid}', 'NotebookController@show')->name('notebooks.show');
        Route::put('notebooks/{hashid}', 'NotebookController@update')->name('notebooks.update');
        Route::delete('notebooks/{hashid}', 'NotebookController@destroy')->name('notebooks.destroy');
        Route::post('notebooks/{hashid}/follow', 'NotebookFollowerController@store')->name('notebooks.follow');
        Route::delete('notebooks/{hashid}/unfollow', 'NotebookFollowerController@destroy')->name('notebooks.unfollow');

        // Page Order
        Route::put('notebooks/{hashid}/pages/sort-order', 'NotebookPageOrderController@update')->name('notebooks.sort-order');

        // Notebook Pages
        Route::get('notebooks/{hashid}/pages', 'PageController@index')->name('pages.index');
        Route::post('notebooks/{hashid}/pages', 'PageController@store')->name('pages.store');
        Route::get('notebooks/{notebook}/pages/{page}', 'PageController@show')->name('pages.show');
        Route::put('notebooks/{notebook}/pages/{page}', 'PageController@update')->name('pages.update');
        Route::delete('notebooks/{notebook}/pages/{page}', 'PageController@destroy')->name('pages.destroy');

        // Page Comments
        Route::get('notebooks/{notebook}/pages/{page}/comments', 'PageCommentController@index')->name('pages.comments.index');
        Route::post('notebooks/{notebook}/pages/{page}/comments', 'PageCommentController@store')->name('pages.comments.store');
        Route::get('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@show')->name('pages.comments.show');
        Route::put('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@update')->name('pages.comments.update');
        Route::delete('notebooks/{notebook}/pages/{page}/comments/{comment}', 'PageCommentController@destroy')->name('pages.comments.destroy');

        // Page Documents
        Route::get('notebooks/{notebook}/pages/{page}/documents', 'PageDocumentController@index')->name('pages.documents.index');
        Route::post('notebooks/{notebook}/pages/{page}/documents', 'PageDocumentController@store')->name('pages.documents.store');
        Route::get('notebooks/{notebook}/pages/{page}/documents/{document}', 'PageDocumentController@show')->name('pages.documents.show');
        Route::put('notebooks/{notebook}/pages/{page}/documents/{document}', 'PageDocumentController@update')->name('pages.documents.update');
        Route::delete('notebooks/{notebook}/pages/{page}/documents/{document}', 'PageDocumentController@destroy')->name('pages.documents.destroy');

        // Page Activity
        Route::get('notebooks/{notebook}/pages/{page}/activity', 'PageActivityController@show')->name('pages.activity.show');
    });

    // Stripe Webhooks
    Route::post(
        'stripe/webhook',
        '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
    );
});
