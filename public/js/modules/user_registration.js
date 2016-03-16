$(function() {
    $('#txtUserName').focusin(function(){
      if($(this).val().trim().length == 0){
          $(this).val('');
      }
    }).focusout(function(){
        if($(this).val().trim().length == 0){
            $(this).val('');
        }
    });
    $('.btn-save-user').click(function(event){
		event.preventDefault();
        gisForm.clickSave(event, {
            formEle : $('.frm-validation-login'),
            callbackFunction : function(data){
                if(data.code == 401)
                    window.location.href = window.base_url+'login';
                var errorMessage = buildMessage(data.message);

                if (data.code == 200) {
                    if (errorMessage !== '') {
                        fancyAlert(errorMessage, Lang.get('common.info_title'));
                        parent.$.fancybox.close();
                        $('.frm-validation-list-user').validationEngine('hideAll');
                        userModel = {
                            username : $('#search-txt-username').val(),
                            user_group_id : $('#search-select-usergroup').val(),
                            user_locked_flg : $('#search-select-userlock').val(),
                            user_code : $('#search-txt-usercode').val(),
                            email : $('#search-txt-email').val()
                        };
                        $("#jqGrid").jqGrid('setGridParam',{ search: true, postData: userModel, mtype: 'POST'}); // Post data for jqgrid
                        gisGrid.refresh(window.base_url + '/admin/search-users');
                        $('div#holdChecked > input').remove();
                    } else {
                        if (typeof data.redirect !== 'undefined') {
                            if (data.redirect !== null) {
                                var url = data.redirect;
                                if (url.length > 0) {
                                    location.href = data.redirect;
                                }
                            }
                        }
                    }
                    
                    $('.frm-validation-login').trigger("reset");
                } else {
                    fancyAlert(errorMessage, Lang.get('common.error_title'));
                    if(data.code == 403){
                    	window.location.href = permission_denined_url;
                    }   
                }
            }
        });
	})
})