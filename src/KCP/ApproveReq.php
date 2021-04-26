<?php
/**
 * Created by PhpStorm.
 * User: evans
 * Date: 2018-06-11
 * Time: 오후 3:36
 */

namespace EvansKim\KCP;


class ApproveReq
{
    public  $accessCredentialType;    // AccessCredentialType
    public  $baseRequestType;         // BaseRequestType
    public  $escrow;                  // boolean
    public  $orderID;                 // string
    public  $paymentAmount;           // string
    public  $paymentMethod;           // string
    public  $productName;             // string
    public  $returnUrl;               // string
    public  $siteCode;                // string
}