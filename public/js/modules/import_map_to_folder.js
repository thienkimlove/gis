/**
 * Created by smagic39 on 6/19/15.
 */
"use strict";
$(function() {
    var $folderDestination = $('#map_name');
    var $displayFileName = $('#displayFileName');
    parent.$('#loading').hide();
    $('#upfile').change(function (event) {
        var file = $(this).val();

            var fileName = file.split("\\");
            var tmpFileName = fileName[fileName.length - 1];
            $displayFileName.val(tmpFileName);
            var $currentValue = $(this).val();
            var $time = moment().format('YYYY年MM月DD日 HH:mm:ss');
             $folderDestination.val(createName(tmpFileName, $time));


    });
    $(document).on('click', '.getFile', function (event) {
        $("#upfile").click();
    });

    $('.btn-import').click(function (event) {

        var validForm =  $('.frm-validation-import').validationEngine('validate', {
            showOneMessage: true
        });
        if(validForm){
           parent.$('#loading').show();
            $('form').submit();

        }
    });

    $('.button-reset-form').click(function(event){
        window.location.reload(true);
        window.location.href = location.href;
    });

    $('#fancybox-error').modal('show');

});
function createName(mapName,time){

    mapName = reformatMapName(mapName);
    if(mapName == ''){
        return null;
    }
    return mapName+'_'+time;
}
function reformatMapName(mapName){
    var tmpName = mapName.split('.');
    return tmpName[0];


}