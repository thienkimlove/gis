

// submit form
$('.btn-changing-user').click(function(event){

    var password = $("#password" ).val();    
    var username = $("#txtUserName" ).val();
    var email = $("#email" ).val();
    
    if(password+username+email+"" ===""){
    	fancyAlert(window.changinguser_message_all_data_empty, window.info_title);
    	return;
    }  
    
     
    gisForm.clickSaveChangingUser(event, {
        formEle : $('.changing-user-form'),
        callbackFunction : function(data){
        	fancyMessage(data.message, window.info_title, function(){
            	if(data.code===1)
            	{
            		location.reload();
            	} 
        	});        
        }
    });
});


(function(module) {
	module.cleanForm = function(){
		//redirect to home page
		window.location.href = window.base_url;
	};  
	  
})(changinguser = {});
