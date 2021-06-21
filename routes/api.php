<?php


Route::group(['namespace'=>'api\v2_1', 'prefix'=>'v2.1'], function () {
    Route::post('/mail/sendAPI/', 'MailController@sendAPI');
});

