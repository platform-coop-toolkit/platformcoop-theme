(function ($) {
    'use strict';

    $('#submit-login').click((event) => {
        event.preventDefault();

        let fields = validateLoginField();

        if (fields) {
    
            $.post(
                wpObj.ajaxUrl,
                {
                    action: 'login_menu',
                    log: fields.username,
                    pwd: fields.password,
                    rememberme: $('#rememberme').val() === 'forever' ? 'forever' : null,
                },
                (res) => {
                    if (!res.success) {
                        $('.login-submit').after('<p class="login-error">Wrong username or password. Try again</p>');
                    } else {
                        location.hash = '';
                        location.reload();
                    }
                }
            )
            .fail((res) => {
                $('.login-submit').after('<p class="login-error">Internal server error!</p>');
            });

        }
    });

    $('a.login[href="#logout"]').click((event) => {
        event.preventDefault();

        $.get(
            wpObj.ajaxUrl,
            { action: 'logout_menu' },
            (res) => {
                location.hash = '';
                location.reload();
            }
        );
    });

    const validateLoginField = () => {
        let $userLogin = $('#user_login');
        let $userPass = $('#user_pass');

        $('.login-error').remove();

        if($userLogin.val() === '') {
            $userLogin.after('<p class="login-error">Required field</p>');
            return false;
        }


        if (!(validateUsername($userLogin.val()) || validateEmail($userLogin.val()))) {
            $userLogin.after('<p class="login-error">Invalid username</p>');
            return false;
        }

        if($userPass.val() === '') {
            $userPass.after('<p class="login-error">Required field</p>');
            return false;
        }

        return {
            username: $userLogin.val(),
            password: $userPass.val(),
        }


    }

    const validateEmail = (email) => {
        return email.match(
          /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };

    const validateUsername = (username) => {
        return username.match(
            /^[a-z0-9_\.]+$/
        );
    };
})(jQuery);