
// submit form
$('.btn-forget-password').click(function(event){
    event.preventDefault();
    gisForm.clickSave(event, {
        formEle : $('.forget-password-form'),
        callbackFunction : function(data){
        	fancyAlert(data.message, window.info_title);             
        }
    });
});

(function(module) {
	
    module.sentEmail = function(url){
    var form = $('#forget-password');
    var valid = true;

    form.validationEngine('validate',{
      onValidationComplete: function(form, status){
          valid = status;
       }
     });

    if(!valid) return;
    $.ajax({
        type: "POST",
        global: true,
        url: url,
        data: form.serialize(),
        success: function(response) {
            fancyAlert(response,"The title");
        }
      });
	};
	  
	  
})(password = {});
