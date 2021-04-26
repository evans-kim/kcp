<?php
$prefix = "api/v1";

Route::prefix($prefix)
    ->group(function(){
        Route::post("/kcp/cash-receipt", "EvansKim\KCP\KcpController@redirectCashReceipt")->name("kcp.payment");
        Route::post("/kcp/payment", "EvansKim\KCP\KcpController@callback")->name("kcp.payment");
        Route::any("/kcp/test", "EvansKim\KCP\KcpController@test")->name("kcp.test");

        Route::any("/kcp/test-mobile", "EvansKim\KCP\KcpController@testMobile")->name("kcp.test.mobile");
        Route::post("/kcp/mobile/approval", "EvansKim\KCP\KcpController@approvalMobile")->name("kcp.mobile.approval");
        Route::any("/kcp/mobile/callback", "EvansKim\KCP\KcpController@callbackMobile")->name("kcp.mobile.callback");
        //Route::get("/kcp/client/{order_id}", "EvansKim\KCPPaymentController@client")->name("kcp.client");
    });

