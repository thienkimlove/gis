// submit form
$('.btn-reset-password').click(function(event){
	//return;
    gisForm.clickSave(event, {
        formEle : $('.reset-password-form'),
        callbackFunction : function(data){
        	fancyMessage(data.message, window.info_title,function(){
                if(data.code===1)
                {
                    window.location = window.base_url;
                }
            });
        }
    });
});


(function(module) {
	
	module.clearForm = function(){
		$('#password').val('');
		$('#password_confirmation').val('');
		
		$(".formError").hide();
	  };
	  
	  
})(resetPassword = {});
