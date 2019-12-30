<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <style>
        input {
            display: block;
            width: 320px;
            margin: 0 auto;
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 5px 10px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
        }

        .logo {
            width: 85px;
            margin: 50px auto;
        }

        .image {
            width: 85px;
            height: 85px;
        }

        .box_01 {
            padding: 10px 0;

        }

        .box {
            position: relative;
            top: -8px;
            height: 40px;
            color: rgb(60, 104, 235);
            font-size: 16px;
            padding: 10px;
        }

        .foot {
            padding: 70px 0;
        }

        .box_02 {

            width: 350px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
        }

        .input_01 {
            width: 60%;
        }

        .get {
            margin-left: 2%;
            background-color: rgb(60, 104, 235);
            width: 30%;
            border-radius: 10px;
            color: white;
            line-height: 30px;
            text-align: center;
        }

        button {
            display: block;
            color: white;
            width: 320px;
            height: 45px;
            margin: 0 auto;
            border-radius: 20px;
            background-color: rgb(60, 104, 235);
        }
    </style>
</head>
<body>
<div class="content padding ">
    <div class="logo">
        <image src="/img/logo.png" class="image"></image>
    </div>
    <div>

        <div class="box_01">
            <label for="iphone" class="box">账号:</label>
            <input type="number" name="phone" placeholder="请输入手机号" id="iphone"/>
        </div>
        <div class="box_01">
            <label for="pay" class="box">支付密码:</label>
            <input type="password" name="paypass" maxlength="6" placeholder="请输入支付密码" id="pay"/>
        </div>
        <div class="box_01">
            <label for="yzm" class="box">验证码:</label>
            <div class="box_02">
                <input type="number" name="code" class="input_01" placeholder="请输入4位验证码" id="yzm"/>
                <div class="get">获取验证码</div>
            </div>

        </div>
        <div class="box_01">
            <label for="inv" class="box">邀请码:</label>
            <input type="number" name="fcode" placeholder="请输入手机号" style="font-size:16px" id="inv"/>
        </div>
        <div class="foot">
            <button form-type="submit" id="sub">提交</button>
        </div>

    </div>
</div>

<script src="http://libs.baidu.com/jquery/1.10.2/jquery.js"></script>
<script>



    $(function () {
        function GetQueryString(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]);
            return null;
        }

        var fcode = GetQueryString("fcode");

        if (fcode != '' && fcode != null) {
            $("#inv").val(GetQueryString("fcode"));
        }

        function onAjaxError(xhr, textStatus, error) {
            if (xhr.status == 422) {
                err = xhr.responseJSON
                $.each(err.errors, function () {
                    $.each(this, function () {
                        alert(this)
                        return;
                    })
                    return;
                });
            } else if (xhr.status === 500) {
                alert('系统错误');
            }
        }
        var regex = /^1[3456789]\d{9}$/;
        var count;
        $('.get').click(function () {
            var phone = $('[name=phone]').val();
            if (!(regex.test(phone))) {
                alert("手机号码有误，请重填");
                return false;
            }
            count = 60;

            $.post('http://123.57.186.157/api/sms', {phone: phone}, function (data) {
                if (data.code == 200) {
                    if(data.is_register==1){
                        alert('该手机已被注册')
                        return;
                    }
                    alert('发送成功');
                    var timer = setInterval(function () {
                        count--;
                        $(".get").html(count + "秒间隔");
                        if (count == 0) {
                            clearInterval(timer);
                            $(".get").attr("disabled", false);//启用按钮
                            $(".get").html("重发验证码");
                            code = "";//清除验证码。如果不清除，过时间后，输入收到的验证码依然有效
                        }
                    }, 1000);
                }
            }, 'json').error(onAjaxError);
        });

        $('#sub').click(function () {
            var phone = $('[name=phone]').val();
            var paypass = $("[name=paypass]").val();
            var code = $("[name=code]").val();
            var fcode = $("[name=fcode]").val();

            if (!(regex.test(phone))) {
                alert('手机号码有误');
                return false;
            }

            if (paypass.length != 6) {
                alert('支付密码必须是6位');
                $("[name=paypass]").val("");
                return false;
            }

            if (code.length != 4 || !/^[0-9]*$/.test(code)) {
                alert('短信验证码错误');
                $("[name=code]").val("");
                return false;
            }

            if (fcode != '') {
                if (fcode.length != 6 || !/^[0-9]*$/.test(fcode)) {
                    alert('邀请码应为6位数字');
                    $("[name=fcode]").val("");
                    return false;
                }
            }

            $.post('http://123.57.186.157/api/register', {
                phone: phone,
                paypass: paypass,
                code: code,
                fcode: fcode,
            }, function (data) {
                if (data.code == 200) {
                    alert('注册成功');
                }
            }, 'json').error(onAjaxError);

        })
    })

</script>

</body>
</html>

