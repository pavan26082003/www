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

Route::middleware('auth:api')->get('/user', function (Request $request) {
   return $request->user();
});

Route::get('testingmail', 'ApiController@testingmail');

Route::get('getwalletamount', 'ApiController@getwalletamount');

Route::get('clearnotifications', 'ApiController@clearnotifications');

Route::post('withdrawlimit', 'ApiController@withdrawlimit');

Route::post('getstripetoken', 'ApiController@getstripetoken');

Route::post('getibanlist', 'ApiController@getibanlist');

Route::post('updateiban', 'ApiController@updateiban');

Route::get('getibandetails', 'ApiController@getibandetails');

Route::get('deleteiban', 'ApiController@deleteiban');

Route::get('primaryiban', 'ApiController@primaryiban');

Route::post('addiban', 'ApiController@addiban');

Route::get('testing', 'ApiController@testing');

Route::get('showticket', 'ApiController@showticket');

Route::get('replyticket', 'ApiController@replyticket');

Route::get('get_tickets', 'ApiController@get_tickets');

Route::get('getqrcode', 'ApiController@getqrcode');

Route::post('support', 'ApiController@support');

Route::get('getnotificationstatus', 'ApiController@getnotificationstatus');

Route::get('getnotifications', 'ApiController@getnotifications');

Route::post('addcard', 'ApiController@addcard');

Route::get('listcards', 'ApiController@listcards');

Route::get('deletecard', 'ApiController@deletecard');

Route::get('primarycard', 'ApiController@primarycard');

Route::post('addmoney', 'ApiController@addmoney');

Route::get('sendmoneyqrscanner', 'ApiController@sendmoneyqrscanner');

Route::get('getphones', 'ApiController@getphones');

Route::get('getdetails', 'ApiController@getdetails');

Route::post('updatedetails', 'ApiController@updatedetails');

Route::get('notificationstatus', 'ApiController@notificationstatus');

Route::get('closeaccount', 'ApiController@closeaccount');

Route::get('searchphone', 'ApiController@searchphone');

Route::get('gethome', 'ApiController@gethome');

Route::get('gdpr', 'ApiController@gdpr');

Route::get('termsandconditions', 'ApiController@termsandconditions');

Route::get('close_ticket', 'ApiController@close_ticket');

Route::get('support_ticket', 'ApiController@support_ticket');

Route::get('getreasons', 'ApiController@getreasons');

Route::get('otpverify', 'ApiController@otpverify');

Route::get('phoneotp_register', 'ApiController@phoneotp_register');

Route::post('sendmoney_payin', 'ApiController@sendmoney_payin');

Route::post('sendmoney', 'ApiController@sendmoney');

Route::post('checkwallet', 'ApiController@checkwallet');

Route::post('reload_payin', 'ApiController@reload_payin');

Route::post('reload_payout', 'ApiController@reload_payout');

Route::post('requestmoney', 'ApiController@requestmoney');

Route::post('withdrawmoney', 'ApiController@withdrawmoney');

Route::post('resetpassword', 'ApiController@resetpassword');

Route::get('apiLogin', 'ApiController@apiLogin');

Route::get('withdrawbalance', 'ApiController@withdrawbalance');

Route::get('transactions', 'ApiController@transactions');

Route::get('orderhistory', 'ApiController@orderhistory1');

Route::post('register', 'ApiController@register');

Route::post('payment', 'ApiController@payment');

Route::post('paymentout', 'ApiController@paymentout');


Route::post('withdraw_pay', 'ApiController@withdraw_pay');

Route::post('withdraw_payout', 'ApiController@withdraw_payout');

Route::get('complaints', 'ApiController@complaints');

Route::get('fraud', 'ApiController@fraud');

Route::get('privacy', 'ApiController@privacy');

Route::get('phoneotp_forgot', 'ApiController@phoneotp_forgot');

Route::get('/', 'ApiController@TransactionVerify');
Route::post('/', 'ApiController@TransactionVerifyError');

Route::post('ibanadd', 'ApiController@ibanadd');

Route::post('payout', 'ApiController@payout');

Route::get('test',function(){
   return response([1,2,3,4],200);
});




Route::get('/display/{modelNumber}/{pageNo}', [YourControllerName::class, 'display']);

// Route for selecting a model
Route::get('/select-model/{userId}', [YourControllerName::class, 'selectModel']);