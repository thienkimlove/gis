var $idOption =  0;
$(document).ready(function(){
  $('#group_id').find('option').each(function(index,val){
      var $textOption = $(val).text().toLowerCase();
      if($textOption == 'admin'){
        $idOption = $(val).val();
        return true;
      }
  });
  $('#group_id').multiselect({
        buttonWidth:170,
        dropRight: true,
        maxHeight:250,
        buttonText: function(options, select) {
            if (options.length === 0) {
                return Lang.get('common.folder_create_group_default');
            }
            else if (this.allSelectedText
                && options.length === $('option', $(select)).length
                && $('option', $(select)).length !== 1
                && this.multiple) {

                if (this.selectAllNumber) {
                    return this.allSelectedText + ' (' + options.length + ')';
                }
                else {
                    return this.allSelectedText;
                }
            }
            else {
                var labels = [];
                options.each(function() {
                    if ($(this).attr('label') !== undefined) {
                        labels.push($(this).attr('label'));
                    }
                    else {
                        labels.push($(this).html());
                    }
                });
                return labels.join(', ') + '';
            }
        }
    });
   
    $('#folderType').on('change', function (e) {
        var valueSelected = this.value;
        if(valueSelected=='bin'||valueSelected=='terrain') {
            $('#group_id').multiselect('selectAll', false);
            $('#group_id').multiselect('updateButtonText');
        }
        else if(valueSelected=='admin'||valueSelected=='fertility'||valueSelected=='fertilizer') {
            $('#group_id').multiselect('deselectAll', false);
            $('#group_id').multiselect('updateButtonText');
            $('#group_id').multiselect('select',[$idOption]);
        }
    });
    });
    
$('#group_id').multiselect('select',[$idOption]);
$('#formID').on('reset', function(){
    setTimeout(function(){
        $('#group_id').multiselect('refresh');
        $('#group_id').multiselect('select',[$idOption]);
    });
});
$(function(){
    $('.btn-save-edit').click(function(event){
        event.preventDefault();
        var title = Lang.get('common.error_title');
        gisForm.clickSave(event, {
            formEle : $('.form-horizontal'),
            callbackFunction : function(data){
            	var message = buildMessage(data.message);
                if (data.code == 200) {
                    title = Lang.get('common.info_title');
                    top.$.fancybox.close();
                    fancyAlertAndLoadPage(message, title);
                }else{
                    if(data.code == 403){
                    	fancyAlertAndLoadPage(message,Lang.get('common.error_title'));
                    }else{
                    	fancyAlert(message,Lang.get('common.error_title'));
                    } 	
                } 
            }
        });
    });
    
    $( ".form-control" ).keydown(function(e) {
    	  if(e.keyCode == 13)
    		  return false;
    });
});