$(function() {
    var userModel =  {
        username : '',
        user_group_id : '',
        user_locked_flg : '',
        code : '',
        email : ''
    };

    gisGrid.init({
        contentEle : $("#jqGrid"),
        url : window.base_url + '/admin/user/get-grid',
        colModel : [
            {
            label : Lang.get('common.user_registration_head_usercode'),
            name : 'user_code',
            width : 75,
            align : 'right'
            //sortable : false
        },
            {label : Lang.get('common.user_registration_head_username'),
            name : 'username',
            width : 75
        //sortable : false
        },
        {
            label : Lang.get('common.user_registration_head_usergroup'),
            name : 'group_name',
            //index : 'usergroups.group_name',
            width : 75
            //sortable : false
        },
            {
                label : Lang.get('common.user_registration_head_userlock'),
                name : 'user_locked_flg',
                width : 40,
                align : 'center',
                formatter : 'checkbox',
                editoptions : {
                    value : '1:0'
                }

                //sortable : false
            }, {
                label : Lang.get('common.user_registration_label_email'),
                name : 'email',
                width : 75
                //sortable : false
            }, {
                label : Lang.get('common.user_registration_head_last_logout'),
                name : 'last_logout_time',
                width : 75,
                align : 'left'
                //sortable : false
            }

        ],
        pager : "#jqGridPager"
    });

    gisGrid.loadData();

    $('.btn-search-user').click(function(event){
    	$('.form-horizontal').validationEngine('validate', {
            showOneMessage:true,
            onValidationComplete: function(form, status){
            	setTimeout(function(){
            		$('.form-horizontal').validationEngine('hideAll');
				}, 4000);
                if(status === false) {
                    return false;
                    
                } else {
            		event.preventDefault();
                    userModel = {
                        username : function() { return $('#search-txt-username').val();},
                        user_group_id : function() { return $('#search-select-usergroup').val();},
                        user_locked_flg : function() { return $('#search-select-userlock').val();},
                        user_code : function() { return $('#search-txt-usercode').val();},
                        email : function() { return $('#search-txt-email').val();}
                    };
                    $("#jqGrid").jqGrid('setGridParam',{ search: true, postData: userModel, mtype: 'POST'}); // Post data for jqgrid
                    gisGrid.refresh(window.base_url + '/admin/search-users');
                }
            }
    	});
    	
    });

    $('.btn-show-edit-user').click(function(event){
     
        event.preventDefault();
        gisForm.clickEdit({
            checkeds : $('div#holdChecked > input'),
            requireMaxMessage : Lang.get('common.select_max'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            callbackReturnUrl : function(value) {
                return window.base_url + '/admin/users/' + value + '/edit'
            }
        });
        // gisGrid.refresh(window.base_url + '/admin/search-users');
    });
    
    $('.btn-delete-user').click(function(event){
        gisForm.clickDelete(event, {
            checkeds : $('div#holdChecked > input'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            formEle : $('.frm-validation-list-user'),
            callbackFunction : function(data){
                if (data.code == 401) {
                    window.location.href = window.base_url + 'login';
                }
                if (data.code == 403) {
                    window.location.href = permission_denined_url;
                }
                if (typeof data.redirect !== 'undefined') {
                    if (data.redirect !== null) {
                        var url = data.redirect;

                        if (url.length > 0) {
                            location.href = data.redirect;
                        }
                    }
                }
                
                var title = 'common.error_title';
                if (data.code == 200) {
                    $('div#holdChecked > input').remove();
                    title = 'common.info_title';
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
                }
                fancyAlert(buildMessage(data.message), Lang.get(title));
                
            }
        });

    });

    $('.btn-show-create-user').click(function(){
        $.fancybox( [{
                href : window.base_url + '/admin/users/create',
                type : 'ajax',
                helpers: {
                    overlay: { closeClick: false } //Disable click outside event
                }
            }], {
                afterLoad: function(data){
                    try {
                        var json = $.parseJSON(data.content);
                        if(json.code == 403){
                            window.location.href = permission_denined_url;
                        }
                        if (json.code == 401) {
                            window.location.href = window.base_url + 'login';
                        }
                        bootbox.dialog({
                            message : json.message,
                            title : Lang.get('common.error_title'),
                            buttons: {
                                success: {
                                    label: Lang.get('common.button_alert_ok'),
                                    callback: function () {
                                        top.$.fancybox.close();
                                    }
                                }
                            }
                        });
                        return false;
                    } catch(err) {

                    }
                }
            }
        );
    });
    
    $(document).on('click','.btn-cancel-popup',function(){
    	$.fancybox.close();
    })
})