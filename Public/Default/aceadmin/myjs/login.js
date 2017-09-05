var Login= function() {
    var handleSubmit = function() {
        $("#loginForm").validate({
            rules : {
                account : {
                    required : true
                },
                password : {
                    required : true
                },
                verify : {
                    required : true
                }
            },
            messages : {
                account : {
                    required : "账号不能为空."
                },
                password : {
                    required : "密码不能为空."
                },
                verify : {
                    required : "验证码不能为空."
                }
            },

            submitHandler : function(form) {
                form.submit();
            }
        });

        $('#loginForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#loginForm').validate().form()) {
                    $('#loginForm').submit();
                }
                return false;
            }
        });
    }
    return {
        init : function() {
            handleSubmit();
        }
    };
}();