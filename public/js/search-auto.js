			$(document).ready(function(){
				var timer = null;
				$('#search-box').keyup(function(e){
						
					if( e.keyCode ==38 ){
						if( $('#search_suggestion_holder').is(':visible') ){
							if( ! $('.selected').is(':visible') ){
								$('#search_suggestion_holder li').last().addClass('selected');
							}else{
								var i =  $('#search_suggestion_holder li').index($('#search_suggestion_holder li.selected')) ;
								$('#search_suggestion_holder li.selected').removeClass('selected');
								i--;
								$('#search_suggestion_holder li:eq('+i+')').addClass('selected');
								
							}
						}
					}else if(e.keyCode ==40){
						if( $('#search_suggestion_holder').is(':visible') ){
							if( ! $('.selected').is(':visible') ){
								$('#search_suggestion_holder li').first().addClass('selected');
							}else{
								var i =  $('#search_suggestion_holder li').index($('#search_suggestion_holder li.selected')) ;
								$('#search_suggestion_holder li.selected').removeClass('selected');
								i++;
								$('#search_suggestion_holder li:eq('+i+')').addClass('selected');
							}
						}					
					}else if(e.keyCode ==13){
						if( $('.selected').is(':visible') ){
							var value	=	$('.selected').text();
							$('#search-box').val(value);
							$('#search_suggestion_holder').hide();
						}
					}else if(e.keyCode == 8){
							var checkEmpty = $(this).val();
							if (checkEmpty.length == 0){
								$('#search_suggestion_holder').hide();
							}else{
								
								var keyword	= $(this).val();
								var token = $('input[name="_token"]').val();
								$.ajax({
									url:window.base_url + '/import-data/ajax-autocomplete',
									type : "POST",
									dateType:"json",
									data : {
										keyword : keyword
									},
									beforeSend : function(request){
							            return request.setRequestHeader('X-CSRF-Token', token);
							        },
							        success : function(data){
						
							            var html = '';
							            for (var item in data){
						
							            	html+='<li class="'+item+'">'+data[item]+'</li>';
							            }
										$('#search_suggestion_holder').html(html);
										$('#search_suggestion_holder').show();
									}
								});
							}
					} else {
						var keyword	= $(this).val();
						var token = $('input[name="_token"]').val();
						$.ajax({
							url:window.base_url + '/import-data/ajax-autocomplete',
							type : "POST",
							dateType:"json",
							data : {
								keyword : keyword
							},
							beforeSend : function(request){
					            return request.setRequestHeader('X-CSRF-Token', token);
					        },
					        success : function(data){
				
					            var html = '';
					            for (var item in data){
				
					            	html+='<li class="'+item+'">'+data[item]+'</li>';
					            }
								$('#search_suggestion_holder').html(html);
								$('#search_suggestion_holder').show();
							}
						});
					}
				});
				
				$('#search_suggestion_holder').on('click','li',function(){
					var value	=	$(this).text();
					$('#user_id').val( $(this).attr('class') );
					$('#search-box').val(value);
					$('#search_suggestion_holder').hide();
				});
				
			});
