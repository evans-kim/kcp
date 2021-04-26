<?php
/**
 * Created by PhpStorm.
 * User: evans
 * Date: 2018-06-07
 * Time: 오후 1:01
 */

namespace EvansKim\KCP;

use Detection\MobileDetect;
use Illuminate\Database\Eloquent\Model;

/**
 * EvansKim\KCP\KcpPayment
 *
 * @property int $id
 * @property string $tno
 * @property string $order_no 주문번호
 * @property int $amount 결제금액
 * @property string $card_cd 카드번호
 * @property string $card_name 카드명
 * @property string $app_time 승인시간
 * @property string $app_no 승인번호
 * @property string $noinf 무이자
 * @property string $quota 할부
 * @property string $partcanc_yn 부분취소여부
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 */
class KcpPayment extends Model
{
    public $config = array();
    private $mode = "test";
    private $g_wsdl           = "";
    private $module_type      = "01";          // 변경불가

    protected $fillable = [
        'tno',
        'order_no',
        'amount',
        'card_cd',
        'card_name',
        'app_time',
        'app_no',
        'noinf',
        'quota',
        'partcanc_yn'
    ];

    public function __construct(array $attributes = [])
    {
        $this->config['home_dir'] = __DIR__;
        $this->config['log_path'] = storage_path("logs/kcp");

        $this->config['log_level'] = "3";
        $this->config['gw_port'] = "8090";
        $this->config['key_dir'] = __DIR__ . "/bin/pub.key";
        $this->setMode();

        parent::__construct( $attributes );
    }
    public function isTest(){
        return config("services.kcp.test_mode");
    }
    public function isMobileAgent(){
        $detector = new \Mobile_Detect();

        if($detector->isMobile())
            return true;
        else
            return false;
    }
    /**
     * @param array|null $order
     * @return string
     * @throws \Throwable
     */
    static public function getPaymentHtmlForm(array $order=null)
    {
        $payment = new static();
        $payment->setMode();

        session()->put(["kcp.order_amount" => $order['due_to_pay']]);

        if( $payment->isMobileAgent() ){
            return view("kcp::mobile-form", ['config'=>$payment->config, 'order'=>$order])->render();
        }else{
            return view("kcp::form", ['config'=>$payment->config, 'order'=>$order])->render();
        }
    }
    static public function getScripts()
    {
        $payment = new static();

        if( $payment->isMobileAgent() ){
            return view("kcp::mobile-script", ['config'=>$payment->config])->render();
        }else{
            return view("kcp::script", ['config'=>$payment->config])->render();
        }
    }

    public function setMode()
    {
        if (!$this->isTest()) {
            $this->config['gw_url'] = "paygw.kcp.co.kr";
            $this->config['js_url'] = "https://pay.kcp.co.kr/plugin/payplus_web.jsp";
            $this->config['site_cd'] = config("services.kcp.site_code");
            $this->config['site_key'] = config("services.kcp.site_key");
            $this->config['site_name'] = config("services.kcp.site_name");
            $this->config['wsdl'] = __DIR__."/includes/real_KCPPaymentService.wsdl";
        } else {
            $this->config['gw_url'] = "testpaygw.kcp.co.kr";
            $this->config['js_url'] = "https://testpay.kcp.co.kr/plugin/payplus_web.jsp";
            $this->config['site_cd'] = "T0000";
            $this->config['site_key'] = "3grptw1.zW0GSo4PQdaGvsF__";
            $this->config['site_name'] = "KCP TEST SHOP";
            $this->config['wsdl'] = __DIR__."/includes/KCPPaymentService.wsdl";
        }

    }

    static function getConfig(){
        $model = (new self());
        return $model->config;
    }
}