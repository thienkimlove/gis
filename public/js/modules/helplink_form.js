$(function(){
    $('.form-edit-helplink').keydown(function(e){

    });
    $('.btn-edit-help-link').click(function(event){
        event.preventDefault();
        gisForm.clickSave(event, {
            formEle : $('.form-edit-helplink'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    $.fancybox.close();
                    gisGrid.refresh();
                } else {
                    fancyAlert(data.message, Lang.get('common.error_title'));
                }
            }
        });
    });
    $('.btnCancelHelplink').click(function(event){
        event.preventDefault();
        $.fancybox.close(true);
    });
});