<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'IndexController@index');
Route::post('/', 'IndexController@index');
Route::any('/resources/landers/{subDomain}/{asset}', 'LanderController@getAsset')->where('asset', '.*');
Route::get('/logout', 'LegacyLoginController@logout');
Route::post('email/incoming', 'RelevanceReactorController@incomingEmail');
Route::post('email/incoming/distribute', 'RelevanceReactorController@distributeEmail');
Route::get('dashboard', 'DashboardController@home');
Route::group(['prefix' => 'users'], function () {
    Route::get('', 'UserController@index')->middleware(['role:0,1,2']);
    Route::get('{id}/affiliates', 'UserController@viewManagersAffiliates');
    Route::group(['prefix' => '/{id}/salary'],
        function () {
            Route::get('create', 'SalaryController@create')->name('salary.create');
            Route::post('create', 'SalaryController@store')->name('salary.create');
            Route::get('update', 'SalaryController@edit')->name('salary.update');
            Route::post('update', 'SalaryController@update')->name('salary.update');
        });
    Route::get('{id}/clicks', 'Report\ClickReportController@user')->name('userClicks');
});
Route::group(['prefix' => 'report'], function () {
    Route::get('daily', 'Report\AggregateReportController@index');
    Route::get('offer', 'Report\OfferReportController@index');

    Route::get('advertiser', 'Report\AdvertiserReportController@index');
    Route::get('blacklist', 'Report\BlackListReportController@index');
    Route::get('adjustments', 'Report\AdjustmentsReportController@index');
    Route::get('sale-log', 'Report\ChatLogReportController@affiliate');

    Route::get('chat-log', 'Report\ChatLogReportController@index');
    Route::get('affiliate', 'Report\EmployeeReportController@index');
    Route::get('chat-log/{userId}', 'Report\ChatLogReportController@admin');

    Route::get('sub', 'Report\SubReportController@show');
    Route::group(['prefix' => 'payout'], function () {
        Route::get('', 'Report\PayoutReportController@report');
        Route::get('pdf', 'Report\PayoutReportController@invoice');
    });
});
Route::resource('offers', 'OfferController')->except('show');
Route::group(['prefix' => 'offers'], function () {
    Route::get('{id}/request', 'OfferController@requestOffer');
    Route::get('{id}/dupe', 'OfferController@dupe');
    Route::get('{id}/delete', 'OfferController@destroy');
    Route::get('{id}/clicks', 'Report\ClickReportController@offer')->name('offerClicks');
    Route::get('mass-assign', 'OfferController@createMassAssign');
    Route::post('mass-assign', 'OfferController@storeMassAssign');
    Route::get('assignableUsers', 'OfferController@getAssignableUsers');
    Route::get("assignedUsers/{id}", 'OfferController@getAssignedUsers');
});
Route::group(['prefix' => 'email/pools'], function () {
    Route::get('', 'EmailPoolController@showAffiliateEmailPools');
    Route::get('{id}/download', 'EmailPoolController@downloadEmailPool');
    Route::get('{id}/claim', 'EmailPoolController@claimEmailPool');
});
Route::group(['prefix' => 'sales'], function () {
    Route::get('add', 'AdjustmentsController@showAddSaleLog');
    Route::post('add', 'AdjustmentsController@createSale');
    Route::get('affiliate-offers/{id}', 'AdjustmentsController@getAffiliatesOffers');
    Route::get('affiliates', 'AdjustmentsController@getAffiliates');
});
Route::group(['prefix' => 'sms'], function () {
    Route::group(['prefix' => 'api'], function () {
        Route::post('messages/send', 'Sms\SmsApiController@sendMessage');
        Route::get('conversations', 'Sms\SmsApiController@getConversations');
        Route::get('conversations/{id}', 'Sms\SmsApiController@getConversation');
        Route::get('conversations/{id}/messages', 'Sms\SmsApiController@getMessages');
        Route::patch('conversations', 'Sms\SmsApiController@patchConversation');
        Route::patch('conversations/{conversationId}/read-new-messages', 'Sms\SmsApiController@readNewMessages');
    });
    Route::get('/', 'Sms\SmsController@getChattingPage');
    Route::group(['prefix' => 'client'], function () {
        Route::get('', 'Sms\SmsClientController@getUsersClient');
        Route::get('add', 'Sms\SmsClientController@create');
        Route::post('add', 'Sms\SmsClientController@store');
        Route::get('edit', 'Sms\SmsClientController@edit');
        Route::post('update', 'Sms\SmsClientController@update');
        Route::post('create', 'Sms\SmsClientController@createSMSWorker');
    });
});
Route::group(['prefix' => 'chat-log'], function () {
    Route::get('add/{pendingConversionId}', 'ChatLogController@showUploadChatLog');
    Route::post('upload', 'ChatLogController@uploadChatLog');
    Route::get('view/{saleLogId}/{fileName}', 'ChatLogController@getSaleLogImage');
});
Route::get("login/{userId}", 'LegacyLoginController@adminLogin');
