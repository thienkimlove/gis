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
        },
        {
            label : Lang.get('common.helplink_popup_header'),
            name : 'popup_screen',
            width : 35,
            sortable:false
        }],
        pager : "#jqGridPager"
    });

    gisGrid.loadData();

    $("#jqGrid").jqGrid(
         'setGridWidth',
         $(".ui-jqgrid").parent().width() - 2
    );

    // Code Reponsive Design Grid - Start
    // On Resize
    $(window).resize(
        function() {
            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }
            window.afterResize = setTimeout(function() {
                $("#jqGrid").jqGrid(
                    'setGridWidth',
                    $(".ui-jqgrid").parent().width() - 2
                );
            }, 500);
    });

    // Code Reponsive Design Grid - End

    

    $('.btn-show-create-helplink').click(function(){
        $.fancybox( [{
                href : window.base_url + '/helplink/create',
                type : 'ajax',
                helpers: {
                    overlay: { closeClick: false } //Disable click outside event
                }
            }], {
                afterLoad: function(data){
                    try {
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

    $('.btn-show-edit-helplink').click(function(event){
        event.preventDefault();
        gisForm.clickEdit({
            checkeds : $('div#holdChecked > input'),
            requireMaxMessage : Lang.get('common.select_max'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            callbackReturnUrl : function(value) {
                return window.base_url + '/helplink/' + value + '/edit'
            }
        });
    });
    $('.btn-show-delete-helplink').click(function(event){
        var errorMsg  = Lang.get('common.error_title');
        gisForm.clickDelete(event, {
            checkeds : $('div#holdChecked > input'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            formEle : $('.frm-validation-list-helplink'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    gisGrid.refresh();
                    //clear checked.
                    $('div#holdChecked > input').remove();
                } else {
                    fancyAlert(data.message, errorMsg);
                }
            }
        });

    });


});
$(document).ready(function(){
    $('.ui-jqgrid-sortable').css({cursor:"default"});
    $('.ui-state-default').css({color: "#ffffff !important"});
});