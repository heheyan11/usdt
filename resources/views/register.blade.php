<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<style>
    #main{margin: 0 auto; width: 800px;}
</style>
<div id="main">
电话<input type="text" name="phone" id="">
密码<input type="password" name="password" id="">

验证码<input type="text" name="code">
    
<input type="button" id="sms" value="发送验证码">

    邀请码 <input type="text" name="fcode" id="fcode">
    <input type="button" id="sub" value="提交">
</div>

<script src="http://libs.baidu.com/jquery/1.10.2/jquery.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(function(){

    function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }

    var fcode =GetQueryString("fcode");
    if(fcode!=''&&fcode!=null){
        $("#fcode").val(GetQueryString("fcode"));
    }

    function onAjaxError(xhr, textStatus, error) {
        if(xhr.status==422){
            err = xhr.responseJSON
            var html = '<div>';
            $.each(err.errors,function(){
                $.each(this,function(){
                    html+= this+'<br>';
                })
            });
            html += '</div>';
            swal({content: $(html)[0], icon: 'error'})
        }else if (xhr.status === 500) {
            alert('系统错误');
        }
    }
    var regex = /^1[3456789]\d{9}$/;
    $('#sms').click(function(){
        var phone = $('[name=phone]').val();
        if(!( regex.test(phone))){
            alert("手机号码有误，请重填");
            return false;
        }
        $.get('/api/sms',{phone,phone},function (data) {
            if(data.code==200){
                alert('发送成功');
            }
        },'json').error(onAjaxError);
    });

    $('#sub').click(function(){
        var phone = $('[name=phone]').val();
        var password=$("[name=password]").val();
        var code=$("[name=code]").val();
        var fcode=$("[name=fcode]").val();
        var token = '{{csrf_token()}}';
        if(!( regex.test(phone))){
            alert('手机号码有误');
            return false;
        }
        if(password.length<6||password.length>12||!/^(?=.*[a-zA-Z]+)(?=.*[0-9]+)[a-zA-Z0-9]+$/.test(password)){
            alert('登陆密码应为6-12位字母和数字组合');
            $("[name=password]").val("");
            return false;
        }
        if(code.length!=4||!/^[0-9]*$/.test(code)){
            alert('短信验证码错误');
            $("[name=code]").val("");
            return false;
        }
        if(fcode.length!=6||!/^[0-9]*$/.test(fcode)){
            alert('邀请码应为6位数字');
            $("[name=fcode]").val("");
            return false;
        }
        $.post('/api/login',{phone:phone,password:password,code:code,fcode:fcode,token:token},function(data){
            if(data.code==200){
                alert('注册成功');
            }
        },'json').error(onAjaxError);

    })
})
</script>
</body>
</html>