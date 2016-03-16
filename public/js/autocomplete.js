
function applyAutocomplete(loading_url,textBoxId,suggestion_holder,hidden_id){

	$(document).ready(function(){
		//var loading_url = window.base_url+'/load-fertilizer-auto';
        if($('#'+textBoxId).val()=='')
            $('#'+suggestion_holder).css('display','none');
		$('#'+textBoxId).keyup(function(e){
			
			if( e.keyCode ==38 ){
				if( $('#'+suggestion_holder).is(':visible') ){
					if( ! $('.selected').is(':visible') ){
						$('#'+suggestion_holder+' li').last().addClass('selected');
					}else{
						var i =  $('#'+suggestion_holder+' li').index($('#'+suggestion_holder+' li.selected')) ;
						$('#'+suggestion_holder+' li.selected').removeClass('selected');
						i--;
						$('#'+suggestion_holder+' li:eq('+i+')').addClass('selected');
						
					}
				}
			}else if(e.keyCode ==40){
				if( $('#'+suggestion_holder).is(':visible') ){
					if( ! $('.selected').is(':visible') ){
						$('#'+suggestion_holder+' li').first().addClass('selected');
					}else{
						var i =  $('#'+suggestion_holder+' li').index($('#'+suggestion_holder+' li.selected')) ;
						$('#'+suggestion_holder+' li.selected').removeClass('selected');
						i++;
						$('#'+suggestion_holder+' li:eq('+i+')').addClass('selected');
					}
				}					
			}else if(e.keyCode ==13){
				if( $('.selected').is(':visible') ){
					var value	=	$('.selected').text();
					$('#'+textBoxId).val(value);
					$('#'+hidden_id).val($('#'+suggestion_holder+' .selected').attr('item_id'));
                    $('#'+hidden_id).trigger('change');
					$('#'+suggestion_holder).hide();
				}
			}else if(e.keyCode == 8){
					var checkEmpty = $(this).val();
					if (checkEmpty.length == 0){
						$('#'+suggestion_holder).hide();
						$('#'+hidden_id).val('');
                        $('#'+hidden_id).trigger('change');
					}else{
						var keyword	= $(this).val();
						$.ajax({
							url:loading_url,
						    global: false, // Disable the ajaxStart trigger
							type : "POST",
							dateType:"json",
							data : {
								keyword : keyword,
								data:$(this).attr('data') //Submit data to server
							},
					        success:function(data){
					            var html = '';
					            for (var item in data){
					        		
					            	html+='<li item_id = "'+item+'" class="'+item+'">'+data[item]+'</li>';
					            }
								$('#'+suggestion_holder).html(html);
								$('#'+suggestion_holder).show();
							}
						});
					}
			} else if($(this).val()){
				var keyword	= $(this).val();

				$.ajax({
					url:loading_url,
				    global: false, // Disable the ajaxStart trigger
					type : "POST",
					dateType:"json",
					data : {
						keyword : keyword,
						data:$(this).attr('data') //Submit data to server
					},

			        success : function(data){
		
			            var html = '';
			            for (var item in data){
		
			            	html+='<li item_id = "'+item+'" class="'+item+'">'+data[item]+'</li>';
			            }
						$('#'+suggestion_holder).html(html);
						$('#'+suggestion_holder).show();
					}
				});
			}
		});
		
		$('#'+suggestion_holder).on('click','li',function(){
			var value	=	$(this).text();
			$('#'+textBoxId).val(value);
			$('#'+hidden_id).val($(this).attr('item_id'));
            $('#'+hidden_id).trigger('change');
			$('#'+suggestion_holder).hide();
		});
		
	});
}



