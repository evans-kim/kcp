<?php
/**
 * Created by PhpStorm.
 * User: evans
 * Date: 2018-06-11
 * Time: 오후 3:34
 */

namespace EvansKim\KCP;


class PayService extends \SoapClient
{
    private   static    $classmap = array(
        'ApproveReq' => '\EvansKim\KCP\ApproveReq',
        'ApproveRes' => '\EvansKim\KCP\ApproveRes',
        'approve' => '\EvansKim\KCP\approve',
        'approveResponse' => '\EvansKim\KCP\approveResponse',
        'AccessCredentialType' => '\EvansKim\KCP\AccessCredentialType',
        'BaseRequestType' => '\EvansKim\KCP\BaseRequestType',
        'BaseResponseType' => '\EvansKim\KCP\BaseResponseType',
        'ErrorType' => '\EvansKim\KCP\ErrorType',
    );

    var   $chatsetType;
    var   $accessCredentialType;
    var   $baseRequestType;
    var   $approveReq;
    var   $approveResponse;
    var   $resCD;
    var   $resMsg;


    public  function  __construct( $wsdl = "", $options = array() )
    {
        foreach( self::$classmap as $key => $value )
        {
            if ( !isset( $options[ 'classmap' ][ $key ] ) )
            {
                $options[ 'classmap' ][ $key ] = $value;
            }
        }

        parent::__construct( $wsdl, $options );

        $accessCredentialType = null;
        $baseRequestType      = null;
        $approveReq           = null;
        $resCD                = "95XX";
        $resMsg               = "연동 오류";
    }

    public  function  setCharSet( $charsetType )
    {
        $this->chatsetType = $charsetType;
    }

    public  function  setAccessCredentialType( $accessLicense,
                                               $signature,
                                               $timestamp )
    {
        $this->accessCredentialType = new AccessCredentialType();

        $this->accessCredentialType->accessLicense  = $accessLicense;
        $this->accessCredentialType->signature      = $signature;
        $this->accessCredentialType->timestamp      = $timestamp;
    }

    public  function  setBaseRequestType( $detailLevel,
                                          $requestApp,
                                          $requestID,
                                          $userAgent,
                                          $version   )
    {
        $this->baseRequestType = new BaseRequestType();

        $this->baseRequestType->detailLevel      = $detailLevel;
        $this->baseRequestType->requestApp       = $requestApp;
        $this->baseRequestType->requestID        = $requestID;
        $this->baseRequestType->userAgent        = $userAgent;
        $this->baseRequestType->version          = $version;
    }

    public  function  setApproveReq( $escrow,
                                     $orderID,
                                     $paymentAmount,
                                     $paymentMethod,
                                     $productName,
                                     $returnUrl,
                                     $siteCode )
    {
        $this->approveReq = new ApproveReq();

        $productName_utf8 = ( $this->chatsetType == "euc-kr" ) ? iconv( "EUC-KR", "UTF-8", $productName ) : $productName;

        $this->approveReq->accessCredentialType = $this->accessCredentialType;
        $this->approveReq->baseRequestType      = $this->baseRequestType;
        $this->approveReq->escrow               = $escrow;
        $this->approveReq->orderID              = $orderID;
        $this->approveReq->paymentAmount        = $paymentAmount;
        $this->approveReq->paymentMethod        = $paymentMethod;
        $this->approveReq->productName          = $productName_utf8;
        $this->approveReq->returnUrl            = $returnUrl;
        $this->approveReq->siteCode             = $siteCode;
    }

    public  function  approve()
    {
        $approve = new approve();

        $approve->req = $this->approveReq;

        $this->approveResponse = $this->__soapCall( "approve", array( $approve ),
            array( 'uri' => 'http://webservice.act.webpay.service.kcp.kr',
                'soapaction' => ''
            )
        );

        $this->resCD  = $this->approveResponse->return->baseResponseType->error->code;
        $this->resMsg = $this->approveResponse->return->baseResponseType->error->message;

        return  $this->approveResponse->return;
    }
}