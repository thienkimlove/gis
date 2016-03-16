$(function() {

    $('.btn-save-buy-fertilizer').click(function(event){
        event.preventDefault();
        gisForm.clickSave(event, {
            formEle : $('.form-buy-fertilizer'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    window.location =window.base_url+'/download-file-csv/'+$("[name='layer_id']").val();
                    $.fancybox.close();
                } else {
                    fancyAlert(data.message, Lang.get('common.error_title'));
                }
            }
        });
    });

    $('.btn-cancel-popup').click(function(event){
        event.preventDefault();
        $.fancybox.close(true);
    });
});
