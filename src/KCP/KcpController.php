<?php
/**
 * Created by PhpStorm.
 * User: evans
 * Date: 2018-06-07
 * Time: 오후 1:42
 */

namespace EvansKim\KCP;



use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KcpController extends Controller
{
    private $redirect = '/shop/my_order_view.php?od_id=';

    public function redirectCashReceipt(Request $request){

        $this->validate($request, ['od_id'=>'required|string']);
        // 결제에 사용할
        $order = Order::where("od_id", $request->od_id)->first();
        /* ============================================================================== */
        /* ============================================================================== */
        /* =   02. 쇼핑몰 지불 정보 설정                                                = */
        /* = -------------------------------------------------------------------------- = */
        // ※ V6 가맹점의 경우
        $g_conf_user_type = "PGNW";   // 변경 불가

        /* ============================================================================== */
        /* =   01. 요청 정보 설정                                                       = */
        /* = -------------------------------------------------------------------------- = */
        $req_tx     = 'pay';                             // 요청 종류
        $trad_time  = Carbon::createFromFormat("Y-m-d H:i:s", $order->od_bank_time)->format("YmdHis");                             // 원거래 시각
        /* = -------------------------------------------------------------------------- = */
        $ordr_idxx  = $order->od_id;                             // 주문 번호
        $buyr_name  = $order->od_name;                             // 주문자 이름
        $buyr_tel1  = $order->od_tel;                             // 주문자 전화번호
        $buyr_mail  = $order->od_email;                             // 주문자 E-Mail
        $good_name  = '캔들웍스 DIY 홈 프레그런스';                             // 상품 정보
        $comment    = '';                             // 비고
        /* = -------------------------------------------------------------------------- = */
        $corp_type     = '0';                      // 사업장 구분
        $corp_tax_type = "TG01";                      // 과세/면세 구분
        $corp_tax_no   = "0000000000";                      // 발행 사업자 번호
        $corp_nm       = "(주)상호";                      // 상호
        $corp_owner_nm = "에반스";                      // 대표자명
        $corp_addr     = "경기도 발전구 미래로 124";                      // 사업장 주소
        $corp_telno    = "0000-0000";                      // 사업장 대표 연락처
        /* = -------------------------------------------------------------------------- = */
        $tr_code    = ($order->od_cash_type === '지출증빙') ? '1' : '0';                             // 발행용도 소득공제 0 지출증빙 1
        $num = preg_replace("/[^0-9]/", '', $order->od_cash_num);
        $id_info    = $num;                             // 신분확인 ID
        $amt_tot    = $order->paid_amount;                             // 거래금액 총 합
        $supplyAmount = round($order->paid_amount / 1.1);
        $amt_sup    = $supplyAmount;                             // 공급가액
        $amt_svc    = '0';                             // 봉사료
        $amt_tax    = $order->paid_amount - $supplyAmount;                             // 부가가치세
        /* = -------------------------------------------------------------------------- = */
        $cust_ip    = $request->ip();                            // 요청 IP
        /* ============================================================================== */

        $tx_cd = "07010000"; // 현금영수증 등록 요청

        $c_PayPlus  = new C_PAYPLUS_CLI;
        $KCP = new KcpPayment();

        $c_PayPlus->mf_clear();
        $rcpt_data_set = '';
        // 현금영수증 정보
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "user_type",      $g_conf_user_type );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "trad_time",      $trad_time        );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "tr_code",        $tr_code          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "id_info",        $id_info          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tot",        $amt_tot          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_sup",        $amt_sup          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_svc",        $amt_svc          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tax",        $amt_tax          );
        $rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_type",       "PAXX"            );

        // 주문 정보
        $c_PayPlus->mf_set_ordr_data( "ordr_idxx",  $ordr_idxx );
        $c_PayPlus->mf_set_ordr_data( "good_name",  $good_name );
        $c_PayPlus->mf_set_ordr_data( "buyr_name",  $buyr_name );
        $c_PayPlus->mf_set_ordr_data( "buyr_tel1",  $buyr_tel1 );
        $c_PayPlus->mf_set_ordr_data( "buyr_mail",  $buyr_mail );
        $c_PayPlus->mf_set_ordr_data( "comment",    $comment   );

        // 가맹점 정보
        $corp_data_set = '';
        $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_type",       $corp_type     );

        if ( $corp_type == "1" ) // 입점몰인 경우 판매상점 DATA 전문 생성
        {
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_type",   $corp_tax_type );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_no",     $corp_tax_no   );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_sell_tax_no",$corp_tax_no   );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_nm",         $corp_nm       );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_owner_nm",   $corp_owner_nm );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_addr",       $corp_addr     );
            $corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_telno",      $corp_telno    );
        }

        $c_PayPlus->mf_set_ordr_data( "rcpt_data", $rcpt_data_set );
        $c_PayPlus->mf_set_ordr_data( "corp_data", $corp_data_set );

        if ( $tx_cd != "" )
        {
            $c_PayPlus->mf_do_tx( "", $KCP->config['home_dir'], $KCP->config['site_cd'], $KCP->config['site_key'], $tx_cd, "",
                $KCP->config['gw_url'], $KCP->config['gw_port'], "payplus_cli_slib", $ordr_idxx,
                $cust_ip, $KCP->config['log_level'], 0, 0, $KCP->config['log_path'] ); // 응답 전문 처리

            $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
            $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
            /* $res_en_msg = $c_PayPlus->mf_get_res_data( "res_en_msg" );  // 결과 영문 메세지 */
        }
        else
        {
            $c_PayPlus->m_res_cd  = "9562";
            $c_PayPlus->m_res_msg = "연동 오류 tran_cd값이 설정되지 않았습니다.";
        }

        if ( $res_cd != "0000" ){
            if($res_cd === 'RC50'){
                abort(406, "현금영수증번호가 없거나 잘못되었습니다.");
            }
            if(!empty($c_PayPlus->m_res_msg)){
                $msg = iconv('euc-kr', 'utf8', $c_PayPlus->m_res_msg);
            }

            abort(500,  "[". $c_PayPlus->m_res_cd ."] ". $msg);
        }

        $cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호

        $c_PayPlus->mf_clear();
        $res_msg = iconv('euc-kr', 'utf-8', $res_msg);
        return ['code'=>$res_cd, 'message'=>$res_msg, 'tid'=>$cash_no];
    }
    public function callbackMobile(Request $request)
    {

        /* ============================================================================== */
        /* =   01. 지불 요청 정보 설정                                                  = */
        /* = -------------------------------------------------------------------------- = */
        $req_tx         = $request->input("req_tx");
        $tran_cd        = $request->input("tran_cd");
        /* = -------------------------------------------------------------------------- = */
        $cust_ip        = $request->ip();
        $ordr_idxx      = $request->input("ordr_idxx");

        /* = -------------------------------------------------------------------------- = */
        $res_cd         = $request->input("res_cd");                         // 응답코드

        if($res_cd == "3001")
        {
            return redirect($this->redirect .$ordr_idxx)->withInput($_POST);
        }

        $use_pay_method = $request->input("use_pay_method");

        $c_PayPlus = new C_PP_CLI();

        $c_PayPlus->mf_clear();

        $KCP = new KcpPayment();

        if ( $req_tx == "pay" )
        {
            $c_PayPlus->mf_set_ordr_data( "ordr_mony",  session("kcp.order_amount") );

            $c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
        }

        if ( $tran_cd != "" )
        {

            $c_PayPlus->mf_do_tx( $_POST[ "trace_no" ], $KCP->config['home_dir'], $KCP->config['site_cd'], $KCP->config['site_key'], $tran_cd, $KCP->config['key_dir'],
                $KCP->config['gw_url'], $KCP->config['gw_port'], "payplus_cli_slib", $ordr_idxx,
                $cust_ip, $KCP->config['log_level'], 0, 0, $KCP->config['log_path']); // 응답 전문 처리

            $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
            $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
        }
        else
        {
            $c_PayPlus->m_res_cd  = "9562";
            $c_PayPlus->m_res_msg = "연동 오류|tran_cd값이 설정되지 않았습니다.";
        }


        if ( $req_tx == "pay" )
        {
            if( $res_cd == "0000" )
            {
                list($data, $tno) = $this->getPaymentResourceFromResponse($ordr_idxx, $c_PayPlus, $use_pay_method);

                $this->tryConfirmPayment($data, $KCP, $c_PayPlus, $tno, $cust_ip, $ordr_idxx);

            }else{
                if ( $res_cd != "0000" )
                {
                    abort(405, "[$res_cd]".$res_msg);
                }
            }

        }
        return redirect($this->redirect .$ordr_idxx);

    }
    public function approvalMobile(Request $request)
    {

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store");
        header("Pragma: no-cache");

        // 쇼핑몰 페이지에 맞는 문자셋을 지정해 주세요.
        $charSetType      = "utf-8";             // UTF-8인 경우 "utf-8"로 설정

        $siteCode         = $request->get("site_cd");
        $orderID          = $request->get("ordr_idxx");
        $paymentMethod    = $request->get("pay_method");
        $escrow           = false;
        $productName      = $request->get("good_name");

        // 아래 두값은 POST된 값을 사용하지 않고 서버에 SESSION에 저장된 값을 사용하여야 함.
        $paymentAmount    = $request->get("good_mny"); // 결제 금액
        $returnUrl        = $request->get("Ret_URL");

        // Access Credential 설정
        $accessLicense    = "";
        $signature        = "";
        $timestamp        = "";

        // Base Request Type 설정
        $detailLevel      = "0";
        $requestApp       = "WEB";
        $requestID        = $orderID;
        $userAgent        = $_SERVER['HTTP_USER_AGENT'];
        $version          = "0.1";

        $config = KcpPayment::getConfig();

        try
        {
            $payService = new PayService( $config['wsdl'] );

            $payService->setCharSet( $charSetType );

            $payService->setAccessCredentialType( $accessLicense, $signature, $timestamp );
            $payService->setBaseRequestType( $detailLevel, $requestApp, $requestID, $userAgent, $version );
            $payService->setApproveReq( $escrow, $orderID, $paymentAmount, $paymentMethod, $productName, $returnUrl, $siteCode );

            $approveRes = $payService->approve();

            Log::debug("kcp-error", [$payService->resCD,  $approveRes->approvalKey,
                $approveRes->payUrl, $payService->resMsg, $returnUrl]);

            printf( "%s,%s,%s,%s", $payService->resCD,  $approveRes->approvalKey,
                                   $approveRes->payUrl, $payService->resMsg );

        }
        catch ( \SoapFault $ex )
        {
            printf( "%s,%s,%s,%s", "95XX", "", "", iconv("EUC-KR","UTF-8","연동 오류 (PHP SOAP 모듈 설치 필요)" ) );
        }
    }
    public function testMobile()
    {
        $kcp = new KcpPayment();
        $kcp->setMode("test");
        return view("kcp::mobile-form", ['config'=>$kcp->config]);
    }
    public function test()
    {
        $kcp = new KcpPayment();
        $kcp->setMode("test");
        return view("kcp::form", ['config'=>$kcp->config]);
    }
    public function callback(Request $request)
    {

        /* ============================================================================== */
        /* =   01. 지불 요청 정보 설정                                                  = */
        /* = -------------------------------------------------------------------------- = */
        $req_tx         = $request->input( "req_tx"); // 요청 종류
        $tran_cd        = $request->input( "tran_cd"); // 처리 종류
        /* = -------------------------------------------------------------------------- = */
        $cust_ip        = $request->ip(); // 요청 IP
        $ordr_idxx      = $request->input( "ordr_idxx"); // 쇼핑몰 주문번호
        $good_name      = $request->input( "good_name"); // 상품명
        /* = -------------------------------------------------------------------------- = */
        $res_cd         = "";                         // 응답코드
        $res_msg        = "";                         // 응답메시지
        $res_en_msg     = "";                         // 응답 영문 메세지
        $tno            = $request->input( "tno"); // KCP 거래 고유 번호
        /* = -------------------------------------------------------------------------- = */
        $buyr_name      = $request->input( "buyr_name"); // 주문자명
        $buyr_tel1      = $request->input( "buyr_tel1"); // 주문자 전화번호
        $buyr_tel2      = $request->input( "buyr_tel2"); // 주문자 핸드폰 번호
        $buyr_mail      = $request->input( "buyr_mail"); // 주문자 E-mail 주소
        /* = -------------------------------------------------------------------------- = */
        $use_pay_method = $request->input( "use_pay_method" ); // 결제 방법

        $KCP = new KcpPayment();
        $c_PayPlus = new C_PP_CLI();

        $c_PayPlus->mf_clear();
        if ( $req_tx == "pay" )
        {
            /* 1 원은 실제로 업체에서 결제하셔야 될 원 금액을 넣어주셔야 합니다. 결제금액 유효성 검증 */

            $c_PayPlus->mf_set_ordr_data( "ordr_mony",  session("kcp.order_amount") );

            $c_PayPlus->mf_set_encx_data( $_POST["enc_data"], $_POST["enc_info"] );
        }
        /* ------------------------------------------------------------------------------ */
        /* =   03.  처리 요청 정보 설정 END                                             = */
        /* ============================================================================== */


        /* ============================================================================== */
        /* =   04. 실행                                                                 = */
        /* = -------------------------------------------------------------------------- = */
        if ( $tran_cd != "" )
        {
            $c_PayPlus->mf_do_tx( "", $KCP->config['home_dir'], $KCP->config['site_cd'], $KCP->config['site_key'], $tran_cd, $KCP->config['key_dir'],
                $KCP->config['gw_url'], $KCP->config['gw_port'], "payplus_cli_slib", $ordr_idxx,
                $cust_ip, $KCP->config['log_level'], 0, 0, $KCP->config['log_path']); // 응답 전문 처리

            $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
            $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
            /* $res_en_msg = $c_PayPlus->mf_get_res_data( "res_en_msg" );  // 결과 영문 메세지 */
        }
        else
        {
            $c_PayPlus->m_res_cd  = "9562";
            $c_PayPlus->m_res_msg = "연동 오류 tran_cd값이 설정되지 않았습니다.";
        }


        if ( $req_tx == "pay" )
        {
            if( $res_cd == "0000" )
            {
                list($data, $tno) = $this->getPaymentResourceFromResponse($ordr_idxx, $c_PayPlus, $use_pay_method);
                // 06-1-1. 신용카드

                $this->tryConfirmPayment($data, $KCP, $c_PayPlus, $tno, $cust_ip, $ordr_idxx);

            }else{
                if ( $res_cd != "0000" )
                {
                    abort(405, "[$res_cd]".$res_msg);
                }
            }

        }
        $order = Order::where('od_id', $ordr_idxx)->first();
        if($order->od_pwd) {
            $url = "/shop/my_order_view.php?od_id=" . $ordr_idxx;
            $url = $url . '&od_pwd=' . $order->od_pwd;
        }else{
            $url = "/shop/payment_confirm.php?od_id=" . $ordr_idxx;
        }
        return redirect($url);
    }

    /**
     * @param $data
     * @param $KCP KcpPayment
     * @param $c_PayPlus C_PP_CLI
     * @param $tno
     * @param $cust_ip
     * @param $ordr_idxx
     *
     */
    protected function tryConfirmPayment($data, $KCP, $c_PayPlus, $tno, $cust_ip, $ordr_idxx)
    {
        try {
            $payment = new OrderPayment();
            $payment->pm_tid = $data['tno'];
            $payment->pm_name = $data['card_name'];
            $payment->pm_amount = $data['amount'];
            $payment->pm_type = 'card';
            $payment->od_id = $data['order_no'];
            $payment->pm_lock = 1;
            $payment->pm_index = 'KCP';
            $payment->pm_receipt_no = $data['app_no'];
            $payment->save();

            $payment = KcpPayment::create($data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            if ($KCP->isTest()) {
                dump($exception->getMessage());
                dump($c_PayPlus);
                exit;
            }
            $c_PayPlus->mf_clear();

            $tran_cd = "00200000";

            $c_PayPlus->mf_set_modx_data("tno", $tno);  // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data("mod_type", "STSC");  // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data("mod_ip", $cust_ip);  // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data("mod_desc", "결과 처리 오류 - 자동 취소");  // 변경 사유

            $c_PayPlus->mf_do_tx("", $KCP->config['home_dir'], $KCP->config['site_cd'], $KCP->config['site_key'], $tran_cd, $KCP->config['key_dir'],
                $KCP->config['gw_url'], $KCP->config['gw_port'], "payplus_cli_slib", $ordr_idxx,
                $cust_ip, $KCP->config['log_level'], 0, 0, $KCP->config['log_path']); // 응답 전문 처리

            $res_cd = $c_PayPlus->m_res_cd;
            $res_msg = $c_PayPlus->m_res_msg;

            abort(405, "[$res_cd]".$res_msg);
        }
    }

    /**
     * @param $ordr_idxx
     * @param $c_PayPlus C_PP_CLI
     * @param $use_pay_method
     * @return array
     */
    protected function getPaymentResourceFromResponse($ordr_idxx, $c_PayPlus, $use_pay_method)
    {
        $data = [];
        $data['order_no'] = $ordr_idxx;
        $tno = $data['tno'] = $c_PayPlus->mf_get_res_data("tno"); // KCP 거래 고유 번호
        $data['amount'] = $c_PayPlus->mf_get_res_data("amount"); // KCP 실제 거래 금액
        $data['app_time'] = $c_PayPlus->mf_get_res_data("app_time"); // 승인시간
        $data['pnt_issue'] = $c_PayPlus->mf_get_res_data("pnt_issue"); // 결제 포인트사 코드

        /* = -------------------------------------------------------------------------- = */
        /* =   05-1. 신용카드 승인 결과 처리                                            = */
        /* = -------------------------------------------------------------------------- = */
        if ($use_pay_method == "100000000000") {
            $data['card_cd'] = $c_PayPlus->mf_get_res_data("card_cd"); // 카드사 코드
            $data['card_name'] = iconv("euc-kr", "utf-8", $c_PayPlus->mf_get_res_data("card_name")); // 카드 종류
            $data['app_no'] = $c_PayPlus->mf_get_res_data("app_no"); // 승인 번호
            $data['noinf'] = $c_PayPlus->mf_get_res_data("noinf"); // 무이자 여부 ( 'Y' : 무이자 )
            $data['quota'] = $c_PayPlus->mf_get_res_data("quota"); // 할부 개월 수
            $data['partcanc_yn'] = $c_PayPlus->mf_get_res_data("partcanc_yn"); // 부분취소 가능유무
        }
        return array($data, $tno);
    }
}
