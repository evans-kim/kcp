
<script type="text/javascript">
    var isIE = false;
    var req01_AJAX;
    var READY_STATE_UNINITIALIZED = 0;
    var READY_STATE_LOADING       = 1;
    var READY_STATE_LOADED        = 2;
    var READY_STATE_INTERACTIVE   = 3;
    var READY_STATE_COMPLETE      = 4;
    var PayUrl = "";

    function displayElement( targetObj, targetText, targetColor )
    {
        if ( targetObj.childNodes.length > 0 )
        {
            targetObj.replaceChild( document.createTextNode( targetText ), targetObj.childNodes[ 0 ] );
        } else
        {
            targetObj.appendChild( document.createTextNode( targetText ) );
        }
        targetObj.style.color = targetColor;
    }

    function clearElement( targetObj )
    {
        for ( var i = ( targetObj.childNodes.length - 1 ); i >= 0; i-- )
        {
            targetObj.removeChild( targetObj.childNodes[ i ] );
        }
    }

    function initRequest()
    {
        if ( window.XMLHttpRequest )
        {
            return new XMLHttpRequest();
        } else if ( window.ActiveXObject )
        {
            isIE = true;
            return new ActiveXObject( "Microsoft.XMLHTTP" );
        }
    }

    function sendRequest( url )
    {
        req01_AJAX = null;
        req01_AJAX = initRequest();

        if ( req01_AJAX )
        {
            req01_AJAX.onreadystatechange = process_AJAX;
            req01_AJAX.open( "POST", url, true );
            req01_AJAX.send( null );
        }
    }

    function kcp_AJAX()
    {
        jsf__chk_type();
        var url = "{{ route('kcp.mobile.approval') }}";
        var form = document.order_info;
        var params = "?site_cd=" + form.site_cd.value
            + "&ordr_idxx=" + form.ordr_idxx.value
            + "&good_mny=" + form.good_mny.value
            + "&pay_method=" + form.pay_method.value
            + "&escw_used=" + form.escw_used.value
            + "&good_name=" + form.good_name.value
            + "&Ret_URL=" + form.Ret_URL.value;
        sendRequest( url + params );
    }

    function process_AJAX()
    {
        if ( req01_AJAX.readyState == READY_STATE_COMPLETE )
        {
            if ( req01_AJAX.status == 200 )
            {
                if ( req01_AJAX.responseText != null )
                {
                    var txt = req01_AJAX.responseText.split(",");

                    // success
                    if( txt[0].replace(/^\s*/,'').replace(/\s*$/,'') == '0000' )
                    {
                        document.getElementById("approval").value = txt[1].replace(/^\s*/,'').replace(/\s*$/,'');
                        //alert("성공적으로 거래가 등록 되었습니다.");
                        PayUrl = txt[2].replace(/^\s*/,'').replace(/\s*$/,'');
                        call_pay_form();
                    }
                    // fail
                    else
                    {
                        alert(txt[3].replace(/^\s*/,'').replace(/\s*$/,''));
                    }
                }
            }
            else
            {
                alert( req01_AJAX.responseText );
            }
        }
        else if ( req01_AJAX.readyState == READY_STATE_UNINITIALIZED )
        {
        }
        else if ( req01_AJAX.readyState == READY_STATE_LOADING )
        {
        }
        else if ( req01_AJAX.readyState == READY_STATE_LOADED )
        {
        }
        else if ( req01_AJAX.readyState == READY_STATE_INTERACTIVE )
        {
        }
    }

    var controlCss = "css/style_mobile.css";
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };
</script>
<script type="text/javascript">
    function doPayment()
    {
        kcp_AJAX();
    }
    /* kcp web 결제창 호츨 (변경불가) */
    function call_pay_form()
    {
        var v_frm = document.order_info;

        if (v_frm.encoding_trans == undefined) {
            v_frm.action = PayUrl;
        } else {
            if (v_frm.encoding_trans.value == "UTF-8") {
                v_frm.action = PayUrl.substring(0, PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";
                v_frm.PayUrl.value = PayUrl;
            } else {
                v_frm.action = PayUrl;
            }
        }

        if (v_frm.Ret_URL.value == "")
        {
            /* Ret_URL값은 현 페이지의 URL 입니다. */
            alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
            return false;
        }
        else
        {
            v_frm.submit();
        }
    }

    /* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
    function chk_pay()
    {
        self.name = "tar_opener";
        var pay_form = document.pay_form;

        if (pay_form.res_cd.value == "3001" )
        {
            alert("사용자가 취소하였습니다.");
            pay_form.res_cd.value = "";
        }

        if (pay_form.enc_info.value)
            pay_form.submit();
    }

    function jsf__chk_type()
    {
        if ( document.order_info.ActionResult.value == "card" )
        {
            document.order_info.pay_method.value = "CARD";
        }
        else if ( document.order_info.ActionResult.value == "acnt" )
        {
            document.order_info.pay_method.value = "BANK";
        }
        else if ( document.order_info.ActionResult.value == "vcnt" )
        {
            document.order_info.pay_method.value = "VCNT";
        }
        else if ( document.order_info.ActionResult.value == "mobx" )
        {
            document.order_info.pay_method.value = "MOBX";
        }
        else if ( document.order_info.ActionResult.value == "ocb" )
        {
            document.order_info.pay_method.value = "TPNT";
            document.order_info.van_code.value = "SCSK";
        }
        else if ( document.order_info.ActionResult.value == "tpnt" )
        {
            document.order_info.pay_method.value = "TPNT";
            document.order_info.van_code.value = "SCWB";
        }
        else if ( document.order_info.ActionResult.value == "scbl" )
        {
            document.order_info.pay_method.value = "GIFT";
            document.order_info.van_code.value = "SCBL";
        }
        else if ( document.order_info.ActionResult.value == "sccl" )
        {
            document.order_info.pay_method.value = "GIFT";
            document.order_info.van_code.value = "SCCL";
        }
        else if ( document.order_info.ActionResult.value == "schm" )
        {
            document.order_info.pay_method.value = "GIFT";
            document.order_info.van_code.value = "SCHM";
        }
    }
</script>