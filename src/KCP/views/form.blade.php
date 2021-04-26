<!-- 주문정보 입력 form 사용자의 환경에 맞도록 수정 -->
<form name="order_info" method="post" action="{{route("kcp.payment")}}" >
    {!! csrf_field() !!}
    <select name="pay_method" style="display: none">
        <option value="100000000000" selected>신용카드</option>
    </select>
    <input type="hidden" name="shop_user_id" value="{{ $order['mb_id'] ?? 1}}">
    <input type="hidden" name="ordr_idxx" class="w200" value="{{ $order['od_id'] ?? date("Ymd-His")  }}" maxlength="40"/>
    <input type="hidden" name="good_name" class="w100" value="상품명"/>
    <input type="hidden" name="good_mny" class="w100" value="{{ $order['due_to_pay']  ?? "1004" }}" maxlength="9"/>
    <input type="hidden" name="buyr_name" class="w100" value="{{ $order['od_name'] ?? "홍길동" }}"/>
    <input type="hidden" name="buyr_mail" class="w200" value="{{ $order['od_email'] ?? "test@test.com" }}"/>
    <input type="hidden" name="buyr_tel1" class="w100" value="{{ $order['od_hp'] ?? "000-0000-0000" }}"/>
    <input type="hidden" name="buyr_tel2" class="w100" value="{{ $order['od_tel'] ?? "000-0000-0000" }}"/>

    <input type="hidden" name="req_tx"          value="pay" />
    <input type="hidden" name="site_cd"         value="{{$config['site_cd']}}" />
    <input type="hidden" name="site_name"       value="{{$config['site_name']}}" />

    <input type="hidden" name="quotaopt"        value="12"/>
    <!-- 필수 항목 : 결제 금액/화폐단위 -->
    <input type="hidden" name="currency"        value="WON"/>
<!-- PLUGIN 설정 정보입니다(변경 불가) -->
    <input type="hidden" name="module_type"     value="01"/>

    <input type="hidden" name="res_cd"          value=""/>
    <input type="hidden" name="res_msg"         value=""/>
    <input type="hidden" name="enc_info"        value=""/>
    <input type="hidden" name="enc_data"        value=""/>
    <input type="hidden" name="ret_pay_method"  value=""/>
    <input type="hidden" name="tran_cd"         value=""/>
    <input type="hidden" name="use_pay_method"  value=""/>

    <!-- 주문정보 검증 관련 정보 : Payplus Plugin 에서 설정하는 정보입니다 -->
    <input type="hidden" name="ordr_chk"        value=""/>

    <!--  현금영수증 관련 정보 : Payplus Plugin 에서 설정하는 정보입니다 -->
    <input type="hidden" name="cash_yn"         value=""/>
    <input type="hidden" name="cash_tr_code"    value=""/>
    <input type="hidden" name="cash_id_info"    value=""/>

    <!-- 2012년 8월 18일 전자상거래법 개정 관련 설정 부분 -->
    <!-- 제공 기간 설정 0:일회성 1:기간설정(ex 1:2012010120120131)  -->
    <input type="hidden" name="good_expr" value="0">


</form>