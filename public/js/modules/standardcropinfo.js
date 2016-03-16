// submit form
$('.btn-save-standard-crop').click(function(event){
    event.preventDefault();
    gisForm.clickSave(event, {
        formEle : $('.standard-crop-info-frm'),
        callbackFunction : function(data){
        	fancyMessage(data.message, window.info_title,
        	function(){
        		if(data.code=== 1) closeFancy();
        	});     
        }
    });
});


//submit form
$('.standard_crop_copying_save_button').click(function(event){
  event.preventDefault();
  gisForm.clickSave(event, {
      formEle : $('.standard-crop-copying-frm'),
      callbackFunction : function(data){
      	fancyMessage(data.message, window.info_title,
      	function(){
      		if(data.code=== 1) closeFancy();
      	});     
      }
  });
});

(function(module){
	
})(standardcropinfo = {});