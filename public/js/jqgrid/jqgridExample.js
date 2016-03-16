$(function() {
    gisGrid.init({
        contentEle : $("#jqGrid"),
        url : window.base_url + '/helpGrid',
        colModel : [ {
            label : Lang.get('common.helplink_address_header'),
            name : 'address',
            width : 75,
            sortable:false
        }, {
            label : Lang.get('common.helplink_help_header'),
            name : 'help',
            width : 75,
            sortable:false
        }],
        pager : "#jqGridPager",
        rowNum : 10,
        checkboxClass : 'chk-usergroup-item'
    });

    gisGrid.loadData();
    $("#jqGrid").jqGrid('setGridWidth',

        jQuery(".ui-jqgrid").parent().width() - 2);

    // Code Reponsive Design Grid - Start
    // On Resize
    $(window).resize(
        function() {

            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }

            window.afterResize = setTimeout(function() {
                $("#jqGrid").jqGrid('setGridWidth',
                    jQuery(".ui-jqgrid").parent().width() - 2);
            }, 500);
        });

    // Code Reponsive Design Grid - End



    // create
    $('.btn-show-create-helplink').click(function(){
        $.fancybox( {href : window.base_url + '/create-helplink', type : 'ajax'} );
    });

    $('.btn-save-help-link').click(function(event){
        event.preventDefault();
        var titleMsg = Lang.get('common.alert_title_message');
        gisForm.clickSave(event, {
            formEle : $('.form-add-helplink'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    fancyAlert(data.message, titleMsg);
                    $.fancybox.close();
                    gisGrid.refresh();
                } else {
                    fancyAlert(data.message, titleMsg);
                }
            }
        });
    });

    // edit
    $('.btn-show-edit-helplink').click(function(event){
        event.preventDefault();
        gisForm.clickEdit({
            checkeds : $('div#holdChecked > input'),
            requireMaxMessage : $('#helplink_edit_ids_max').val(),
            requireMessage : $('#helplink_edit_ids_required').val(),
            errorTitle : Lang.get('common.alert_title_message'),
            callbackReturnUrl : function(value) {
                return window.base_url + '/view-helplink/' + value;
            }
        });
    });

    $('.btn-edit-help-link').click(function(event){
        event.preventDefault();
        var titleMsg = Lang.get('common.alert_title_message');
        gisForm.clickSave(event, {
            formEle : $('.form-edit-helplink'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    fancyAlert(data.message, titleMsg);
                    $.fancybox.close();
                    gisGrid.refresh();
                } else {
                    fancyAlert(data.message, titleMsg);
                }
            }
        });
    });

    // delete
    $('.btn-show-delete-helplink').click(function(event){
        var delete_success_msg = Lang.get('common.helplink_delete_user_success');
        var titleMsg = Lang.get('common.alert_title_message');
        gisForm.clickDelete(event, {
            checkeds : $('div#holdChecked > input'),
            requireMessage : $('#helplink_delete_ids_required').val(),
            errorTitle : Lang.get('common.alert_title_message'),
            formEle : $('.frm-validation-list-helplink'),
            callbackFunction : function(data){
                fancyAlert(delete_success_msg, titleMsg);
                $.fancybox.close();
                gisGrid.refresh();
            }
        });
    });

    // close
    $(document).on("click", ".btnCancelHelplink", function(event) {
        $.fancybox.close(true);
    });
});