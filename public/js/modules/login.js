
$(function() {
    var loginError= 0;
	$('.button-submit').click(function() {
		return $('.frm-validation-login').validationEngine('validate', {
			showOneMessage:true,
			onValidationComplete : function(form, status) {
				setTimeout(function(){
					$('.frm-validation-login').validationEngine('hideAll');
				}, 4000);
				if (status === false)
					return false;
				else {
					$('form[name=login-frm]').submit();
				}

			}
		});
	})

	$('input[type=text],input[type=password]').keydown(function(e) {
		if (e.keyCode == 13) {
			return $('.frm-validation-login').validationEngine('validate', {
				showOneMessage:true,
				onValidationComplete : function(form, status) {
					if (status === false)
						return false;
					else {
						$('form[name=login-frm]').submit();
					}

				}
			});
		}
	})

	$('form[name=login-frm]').submit(function(event) {
		submitAjaxRequest($(this), event, function(data, status, xhr) {
            if (data.code == 423) {
                if(loginError==1) {
                    window.location = window.base_url + '/forget-password';
                }
                else{
                    loginError=1;
                    if (typeof data.message !== 'undefined')
                        fancyAlert(data.message, Lang.get('common.error_title'));
                }
            }
            else {
                if (typeof data.message !== 'undefined' && data.message !== null) {
                    var errorMessage = '';
                    if (typeof data.message === 'string') {
                        errorMessage = data.message;
                    } else {
                        for (i = 0; i < data.message.length; i++) {
                            errorMessage += data.message[i] + "\n";
                        }
                    }
                }
                if (typeof errorMessage !== 'undefined') {
                    fancyAlert(errorMessage, Lang.get('common.error_title'));
                } else {
                    if (typeof data.redirect !== 'undefined') {
                        var url = data.redirect;

                        if (url.length > 0) {
                            location.href = data.redirect;
                        }
                    }
                }
            }
		}, function(data, status, xhr) {
			console.log(status)
		})
	});
})
