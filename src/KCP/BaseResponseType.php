<?php
/**
 * Created by PhpStorm.
 * User: evans
 * Date: 2018-06-11
 * Time: 오후 3:37
 */

namespace EvansKim\KCP;


class BaseResponseType
{
    public  $detailLevel;             // string
    public  $error;                   // ErrorType
    public  $messageID;               // string
    public  $release;                 // string
    public  $requestID;               // string
    public  $responseType;            // string
    public  $timestamp;               // string
    public  $version;                 // string
    public  $warningList;             // ErrorType
}