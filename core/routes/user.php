<?php

use Illuminate\Support\Facades\Route;

Route::
        namespace('User\Auth')->name('user.')->group(function () {

            Route::controller('LoginController')->group(function () {
                Route::get('/login', 'showLoginForm')->name('login');
                Route::post('/login', 'login');
                Route::get('logout', 'logout')->name('logout');
            });

            Route::controller('RegisterController')->group(function () {
                Route::get('register', 'showRegistrationForm')->name('register');
                Route::post('register', 'register')->middleware('registration.status');
                Route::post('check-mail', 'checkUser')->name('checkUser');
            });

            Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                Route::get('reset', 'showLinkRequestForm')->name('request');
                Route::post('email', 'sendResetCodeEmail')->name('email');
                Route::get('code-verify', 'codeVerify')->name('code.verify');
                Route::post('verify-code', 'verifyCode')->name('verify.code');
            });
            Route::controller('ResetPasswordController')->group(function () {
                Route::post('password/reset', 'reset')->name('password.update');
                Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
            });
        });

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');
                Route::get('login/history', 'userLoginHistory')->name('login.history');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                Route::get('/transfer', 'indexTransfer')->name('balance.transfer');
                Route::post('/transfer', 'balanceTransfer')->name('balance.transfer.post');
                Route::post('/search-user', 'searchUser')->name('search.user');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');

                Route::get('bv-log', 'bvLog')->name('bv.log');
                Route::get('referrals', 'myReferralLog')->name('my.referral');
                Route::get('/tree/{user?}', 'binaryTree')->name('binary.tree');

                Route::middleware('kyc')->group(function () {
                    Route::get('transfer-balance', 'balanceTransfer')->name('balance.transfer');
                    Route::post('transfer-balance', 'transferConfirm');
                });

                Route::get('ranking', 'ranking')->name('ranking');

                Route::get('/energy_shop', 'energy_shop_index')->name('energy_shop.index');
                Route::get('/buy_energy', 'buy_energy')->name('energy_shop.buy');
                Route::get('/sell_energy', 'sell_energy')->name('energy_shop.sell');
                Route::post('/charge_energy', 'charge_energy')->name('energy_shop.charge_energy');
                Route::post('/sell_energy_confirm', 'sell_energy_confirm')->name('energy_shop.sell_energy_confirm');
                Route::post('/energy_history', 'energy_history')->name('energy_shop.energy_history');
            });

            Route::controller('EnergyHistoryController')->group(function(){
                Route::get('/', 'index')->name('energy_history');
            });

            Route::controller('PlanController')->prefix('plan')->name('plan.')->group(function () {
                Route::get('/', 'planIndex')->name('index');
                Route::post('/', 'planPurchase')->name('purchase');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            //E-pin Recharge
            Route::controller('EpinController')->group(function () {
                Route::get('e-pin/recharge', 'epin')->name('epin.recharge');
                Route::get('e-pin/recharge/log', 'epinRechargeLog')->name('recharge.log');
                Route::post('e-recharge', 'eRecharge')->name('erecharge');
                Route::post('pin/generate', 'pinGenerate')->name('pin.generate');
            });


            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });

            // Tasks
            Route::controller('TaskController')->prefix('task')->name('task.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/start', 'start')->name('start');
                Route::post('/download_video', 'download_video')->name('download_video');
                Route::post('/check_result', 'check_result')->name('check_result');
                Route::get('/quiz', 'handle_task')->name('quiz');
                Route::get('/history', 'task_trans_history')->name('history');
            });

            
        });

        // Payment
        Route::middleware('registration.complete')->prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });


    });
});
