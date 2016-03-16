$(function(){
    $('.btn-save-usergroup').click(function(event){
        event.preventDefault();
        var titleMsg = Lang.get('common.usergroup_error_title')
        gisForm.clickSave(event, {
            formEle : $('.frm-validation-login'),
            callbackFunction : function(data){
                reloadPage(data);
                if (data.code == 200) {
                    $.fancybox.close();
                    gisGrid.refresh();
                } else {
                    fancyAlert(data.message, titleMsg);
                }
            }
        });
    });
    $('.btn-cancel-popup').click(function(event){
        event.preventDefault();
        $.fancybox.close(true);
    });
});