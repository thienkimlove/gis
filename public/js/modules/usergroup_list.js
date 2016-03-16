$(function() {
    gisGrid.init({
        contentEle : $("#jqGrid"),
        url : window.base_url + '/groupGrid',
        colModel : [ {
            label : Lang.get('common.usergroup_label_name'),
            name : 'group_name',
            width : 75,
            sortable:false
        }, {
            label : Lang.get('common.usergroup_label_desc'),
            name : 'description',
            width : 75,
            sortable:false
        }],
        pager : "#jqGridPager"
    });

    gisGrid.loadData('group_list');

    $('.btn-show-edit-usergroup').click(function(event){
        event.preventDefault();
        gisForm.clickEdit({
            checkeds : $('div#holdChecked > input'),
            requireMaxMessage : Lang.get('common.select_max'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.usergroup_error_title'),
            callbackReturnUrl : function(value) {
                return window.base_url + '/admin/groups/' + value + '/edit'
            }
        });

    });
    $('.btn-show-delete-usergroup').click(function(event){
        var errorMsg  = Lang.get('common.usergroup_error_title');
        gisForm.clickDelete(event, {
            checkeds : $('div#holdChecked > input'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.usergroup_error_title'),
            formEle : $('.frm-validation-list-usergroup'),
            callbackFunction : function(data){
                var message = buildMessage(data.message);
                var title = Lang.get('common.error_title');
            	if (data.code == 401) {
                    window.location.href = window.base_url + 'login';
                }
                if(data.code == 403){
                    window.location.href = permission_denined_url;
                }
                if(data.code == 23503){
                    fancyAlert(message, title);
                }
                if (typeof data.redirect !== 'undefined') {
                    if (data.redirect !== null) {
                        var url = data.redirect;

                        if (url.length > 0) {
                            location.href = data.redirect;
                        }
                    }
                }
                if(data.code == 200) {
                    fancyMessage(message);
                    gisGrid.refresh(window.base_url + '/groupGrid');
                    //clear checked.
                    $('div#holdChecked > input').remove();
                }
                if(data.code == 2000) {
                    fancyAlert(message, title);
                }
            }
        });

    });

    $('.btn-show-create-usergroup').click(function(){
        $.fancybox( [{
                href : window.base_url + '/admin/groups/create',
                type : 'ajax',
                helpers: {
                    overlay: { closeClick: false } //Disable click outside event
                }
            }], {
                afterLoad: function(data){
                    try {
                        console.log(data);
                        var json = $.parseJSON(data.content);
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
                        setTimeout(function() {
                            reloadPage(data);
                        }, 3000);
                        return false;
                    } catch(err) {

                    }
                },
                afterClose : function(){
                    gisGrid.refresh();
                    $('div#holdChecked > input').remove();
                }
            }
        );
    });

});
$(document).ready(function(){
    $('.ui-jqgrid-sortable').css({cursor:"default"});
    $('.ui-state-default').css({color: "#ffffff !important"});
});