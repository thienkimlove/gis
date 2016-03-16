$(function(){
	var errorMessages = $('.errorMessageApplication').val();
	var levelMessage = $('.levelResponseMessage').val();

	if(typeof errorMessages != 'undefined'){
		bootbox.dialog({
		  message: errorMessages,
		  onEscape:function(){},
		  backdrop: true
      	});
	}
})
        