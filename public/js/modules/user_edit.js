$(function() {
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
                        $("#jqGrid").find('input[class="cbox"]').prop('checked',false).parents('tr').removeClass('ui-state-highlight').attr('aria-selected','false');
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
                } else {
                    fancyAlert(errorMessage,Lang.get('common.error_title'));
                    if(data.code == 404){
                        parent.jQuery.fancybox.close();
                        gisGrid.refresh();
                    }else if(data.code == 403){
                    	window.location.href = permission_denined_url;
                    }    	
                }
            }
        });
	})
})