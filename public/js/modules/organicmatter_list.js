/**
 * Created by totoro on 17/07/15.
 */
"use strict";
$(window).resize(function() {
    $(".ui-dialog-content").dialog("option", "position", {my: "center", at: "center", of: window});
});
$(function(){
	var message_compost1 = $('#compost_message1').val();
	var message_compost2 = $('#compost_message2').val();
	var message_greenmanure = $('#greenmanure_message').val();
	$('.btn-byproduct').click(function () {
        loadGridTable.saveGrids();
        return $('.frm-validation-byproduct').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function(form, status){
                    setTimeout(function(){
                        $('.frm-validation-byproduct').validationEngine('hideAll');
                    }, 4000);
                    if(status === false){
                    	return false;
                    }else{
                    	if($('#byproduct-nito').val() === ''){
                    		var n = $('#sub-byproduct-nito').val();
                    	}else {
                    		var n = $('#byproduct-nito').val();
                    	}
                    	if($('#byproduct-photpho').val() === ''){
                    		var p = $('#sub-byproduct-photpho').val();
                    	}else {
                    		var p = $('#byproduct-photpho').val();
                    	}
                    	if($('#byproduct-kali').val() === ''){
                    		var k = $('#sub-byproduct-kali').val();
                    	}else {
                    		var k = $('#byproduct-kali').val();
                    	}
                    	creatingmap.updateRow(1,n,p,k);
                        creatingmap.sumTable3();
                        $('#dialog1').dialog('close');
                    }
                }
            });
    });
	
    $('.btn-greenmanure').click(function () {
        loadGridTable.saveGrids();
        return $('.frm-validation-greenmanure').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function(form, status){
            	if(status === true){
            		if( $('#selectgm3').val() === '2' && $("[name='crops_id']").val() > 4 && $('#kali-rate').val() === ''){
            			$('#kali-rate').validationEngine('showPrompt', message_greenmanure, 'error', 'bottomRight:-100', true);
            			status = false;
            		}
            	}
                setTimeout(function(){
                    $('.frm-validation-greenmanure').validationEngine('hideAll');
                }, 4000);
                if(status === false){
                	return false;
                }else{
                	if($('#greenmanure-nito').val() === ''){
                		var n = $('#sub-greenmanure-nito').val();
                	}else {
                		var n = $('#greenmanure-nito').val();
                	}
                	if($('#greenmanure-photpho').val() === ''){
                		var p = $('#sub-greenmanure-photpho').val();
                	}else {
                		var p = $('#greenmanure-photpho').val();
                	}
                	if($('#greenmanure-kali').val() === ''){
                		var k = $('#sub-greenmanure-kali').val();
                	}else {
                		var k = $('#greenmanure-kali').val();
                	}
                	creatingmap.updateRow(2,n,p,k);
                    creatingmap.sumTable3();
                    $('#dialog1').dialog('close');
                }
            }
        });
    });

    $('#kali-rate').keyup(function() {
    	if( $('#selectgm3').val() === '2' && $("[name='crops_id']").val() > 4 && $('#kali-rate').val() === ''){
    		$('#kali-rate').validationEngine('showPrompt', message_greenmanure, 'error', 'bottomRight:-100', true);
    	}else if( $('#selectgm3').val() === '2' && $("[name='crops_id']").val() > 4 && $('#kali-rate').val() !== null){
    		if($('#kali-rate').validationEngine('validate')){
    			creatingmap.getDataGM();
    		}else {
    			setTimeout(function(){
    			    $('.frm-validation-greenmanure').validationEngine('hideAll');
    			}, 4000);
    		}
    	}
    });
    
    $('.btn-compost').click(function () {
        loadGridTable.saveGrids();
        return $('.frm-validation-compost').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function(form, status){
            	if(status === true){
            	if( $('#selectcp3').val() === '1' && $('#dry-matter').val() === ''){
            		$('#dry-matter').validationEngine('showPrompt', message_compost1, 'error', 'bottomRight:-100', true);
            		status = false;
            	}else if ( $('#selectcp3').val() === '2'){
            		 if($('#seibun-nito').val() === ''){
            			 $('#seibun-nito').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
            			 status = false;
            		 }
            		 else if($('#seibun-photpho').val() === ''){
            			 $('#seibun-photpho').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
            			 status = false;
            		 }
            		 else if($('#seibun-kali').val() === ''){
            			 $('#seibun-kali').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
            			 status = false;
            		 }
            	}
            	}
                setTimeout(function(){
                    $('.frm-validation-compost').validationEngine('hideAll');
                }, 4000);
                if(status === false){
                	return false;
                }else{
                	if($('#compost-user-nito').val() === ''){
                		var n = $('#compost-nito').val();
                	}else {
                		var n = $('#compost-user-nito').val();
                	}
                	if($('#compost-user-photpho').val() === ''){
                		var p = $('#compost-photpho').val();
                	}else {
                		var p = $('#compost-user-photpho').val();
                	}
                	if($('#compost-user-kali').val() === ''){
                		var k = $('#compost-kali').val();
                	}else {
                		var k = $('#compost-user-kali').val();
                	}
                	creatingmap.updateRow(3,n,p,k);
                    creatingmap.sumTable3();
                    $('#dialog1').dialog('close');
                }
            }
        });
    });
    
    $('#dry-matter').keyup(function() {
    	if( $('#selectcp3').val() === '1' && $('#dry-matter').val() === ''){
    		$('#dry-matter').validationEngine('showPrompt', message_compost1, 'error', 'bottomRight:-100', true);
    	}else if( $('#selectcp3').val() === '1' && $('#dry-matter').val() !== null){
    		if($('#dry-matter').validationEngine('validate')){
				creatingmap.getDataCP();
                creatingmap.npkRecommend();
    		}else {
    			setTimeout(function(){
    			    $('.frm-validation-compost').validationEngine('hideAll');
    			}, 4000);
    		}
    	}
    });
    
    $('#compost-input').keyup(function() {
    	creatingmap.npkRecommend();
    });

    $('#sub-compost-nito').keyup(function() {
    	creatingmap.npkRecommend();
    });

    $('#sub-compost-photpho').keyup(function() {
    	creatingmap.npkRecommend();
    });

    $('#sub-compost-kali').keyup(function() {
    	creatingmap.npkRecommend();
    });

    $('#seibun-nito').keyup(function() {
    	if ( $('#selectcp3').val() === '2'){
   		 if($('#seibun-nito').val() === '')
   			 $('#seibun-nito').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
    	}
    	setTimeout(function(){
		    $('.frm-validation-compost').validationEngine('hideAll');
		}, 4000);
    	creatingmap.npkRecommend();
    });
    
    $('#seibun-photpho').keyup(function() {
    	if ( $('#selectcp3').val() === '2'){
      		 if($('#seibun-photpho').val() === '')
      			 $('#seibun-photpho').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
       	}
    	setTimeout(function(){
		    $('.frm-validation-compost').validationEngine('hideAll');
		}, 4000);
    	creatingmap.npkRecommend();
    });
    
    $('#seibun-kali').keyup(function() {
    	if ( $('#selectcp3').val() === '2'){
      		 if($('#seibun-kali').val() === '')
      			 $('#seibun-kali').validationEngine('showPrompt', message_compost2, 'error', 'bottomRight:-100', true);
       	}
    	setTimeout(function(){
		    $('.frm-validation-compost').validationEngine('hideAll');
		}, 4000);
    	creatingmap.npkRecommend();
    });
})