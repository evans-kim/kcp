
<script type="text/javascript">
    /****************************************************************/
    /* m_Completepayment  설명                                      */
    /****************************************************************/
    /* 인증완료시 재귀 함수                                         */
    /* 해당 함수명은 절대 변경하면 안됩니다.                        */
    /* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
    /* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
    /* EXE 방식의 경우 리턴 값이 json 으로 넘어옴                   */
    /****************************************************************/
    function m_Completepayment( FormOrJson, closeEvent )
    {
        var frm = document.order_info;

        /********************************************************************/
        /* FormOrJson은 가맹점 임의 활용 금지                               */
        /* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
        /* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
        /********************************************************************/
        GetField( frm, FormOrJson );


        if( frm.res_cd.value == "0000" )
        {
            frm.submit();
        }
        else
        {
            alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );

            closeEvent();
        }
    }
</script>

<script type="text/javascript" src='{{$config['js_url']}}'></script>

<script type="text/javascript">
    /* 플러그인 설치(확인) */
    //kcpTx_install(); // Plugin 결제창 호출 방식인 경우 적용하시기 바랍니다.

    /* Payplus Plug-in 실행 */
    function jsf__pay( form )
    {
        try
        {
            KCP_Pay_Execute( form );
        }
        catch (e)
        {
            /* IE 에서 결제 정상종료시 throw로 스크립트 종료 */
        }
    }

    function doPayment() {

        jsf__pay(document.order_info);
    }

</script>