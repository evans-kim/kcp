<!-- 주문정보 입력 form 사용자의 환경에 맞도록 수정 -->
<form name="order_info" method="post" style="display: none;">

    <input type="hidden" name="encoding_trans" value="UTF-8" >
    <input type="hidden" name="PayUrl" >
    <select name="ActionResult" onchange="jsf__chk_type();" style="display: none;">
        <option value="card" selected>신용카드</option>
    </select>
    <input type="hidden" name="shop_user_id" value="{{ $order['mb_id'] ?? 1}}">
    <input type="hidden" name="ordr_idxx" class="w200" value="{{ $order['od_id'] ?? date("Ymd-His")  }}" maxlength="40"/>
    <input type="hidden" name="good_name" class="w100" value="캔들웍스 DIY 홈 프레그런스"/>
    <input type="hidden" name="good_mny" class="w100" value="{{ $order['due_to_pay'] ?? "1004" }}" maxlength="9"/>
    <input type="hidden" name="buyr_name" class="w100" value="{{ $order['od_name'] ?? "홍길동" }}"/>
    <input type="hidden" name="buyr_mail" class="w200" value="{{ $order['od_email'] ?? "test@test.com" }}"/>
    <input type="hidden" name="buyr_tel1" class="w100" value="{{ $order['od_hp'] ?? "000-0000-0000" }}"/>
    <input type="hidden" name="buyr_tel2" class="w100" value="{{ $order['od_tel'] ?? "000-0000-0000" }}"/>
    <!--//footer-->

    <!-- 공통정보 -->
    <input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
    <input type="hidden" name="site_cd"         value="{{ $config['site_cd'] }}">      <!-- 사이트 코드 -->
    <input type="hidden" name="shop_name"       value="{{ $config['site_name'] }}">      <!-- 사이트 이름 -->
    <input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
    <input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->
    <!-- 결제등록 키 -->
    <input type="hidden" name="approval_key"    id="approval">
    <!-- 인증시 필요한 파라미터(변경불가)-->
    <input type="hidden" name="escw_used"       value="N">
    <input type="hidden" name="pay_method"      value="">
    <input type="hidden" name="van_code"        value="">
    <!-- 신용카드 설정 -->
    <input type="hidden" name="quotaopt"        value="12"/>                           <!-- 최대 할부개월수 -->
    <!-- 가상계좌 설정 -->
    <input type="hidden" name="ipgm_date"       value=""/>
    <!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
    <input type="hidden" name="pt_memcorp_cd"   value=""/>
    <!-- 현금영수증 설정 -->
    <input type="hidden" name="disp_tax_yn"     value="Y"/>
    <!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
    <input type="hidden" name="Ret_URL"         value="{{ route("kcp.mobile.callback") }}">
    <!-- 화면 크기조정 -->
    <input type="hidden" name="tablet_size"     value="1">

    <!-- 추가 파라미터 ( 가맹점에서 별도의 값전달시 param_opt 를 사용하여 값 전달 ) -->
    <input type="hidden" name="param_opt_1"     value="">
    <input type="hidden" name="param_opt_2"     value="">
    <input type="hidden" name="param_opt_3"     value="">

</form>