$(document).on("keydown", "input", function(e) {
    if (e.which==13) e.preventDefault();
});
// submit form
$(document).ready(function() {
    $('.btn-save-fertilizer').click(function (event) {
        event.preventDefault();
        gisForm.clickSave(event, {
            formEle: $('.frm-fertilizer-info'),
            callbackFunction: function (data) {
                if (data.code === 200) {
                    fancyMessage(data.message, window.info_title,
                        function () {
                            closeFancy();
                            fertilizer.refresh();//location.reload();
                        });
                }
                else if (data.code === 409) {
                    showConfirm(data.message, window.error_title,
                        function () {
                            $('#agreedSave').val(1);
                            var form = $('form[name=frm-fertilizer-info]');
                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: form.serialize(),
                                success: function (data) {
                                    closeFancy();
                                    fertilizer.refresh();
                                }
                            });

                        });
                }
                else if (data.code === 0){
                    fancyAlert(data.message, window.error_title);
                    closeFancy();
                    fertilizer.refresh();
                }
                else{
                    fancyAlert(data.message, window.error_title);
                }
            }
        });
    });
});


(function(module){

	
	module.loadGridData = function(){
		var lastsel = '123';
		$("#jqGridInfo")
		.jqGrid({
			//url: 'get-paging-data/'+JSON.stringify(module.userModel),
			url: 'get-fertilizers',
			datatype: "json",
			 colModel: [
	 			{ 
	 				label: Lang.get('common.fertilizer_info_n'),
	 				name: 'crop.crops_name', 
	 				width: 80,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_info_n_fertilization_standard_amount'),
	 				name: 'fertilization_standard_name', 
	 				width: 80,
	 				sortable:false, 
	 				editable:true
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_info_p'),
	 				name: 'fertilizer_range', 
	 				width: 80,
	 				sortable:false,
	 				editable:true
	 				
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_info_p_fertilization_standard_amount'),
	 				name: 'notes', 
	 				width: 80,
	 				sortable:false,
	 				editable:true
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_info_k'),
	 				name: 'remarks', 
	 				width: 80,
	 				sortable:false,
	 				editable:true
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_info_k_fertilization_standard_amount'),
	 				name: 'remarks', 
	 				width: 80,
	 				sortable:false,
	 				editable:true
	 			}
		 			
			],
			autowidth: true,
            scroll: 0,
	        jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id' 
           	rowNum:10,
           	rowList:[10,20,30],
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			width: 800,
			height: 278,
			loadonce: false,	
			scrollOffset:0,
			onSelectRow: function(id){				
				if(id && id!==lastsel){
					jQuery('#jqGridInfo').jqGrid('restoreRow',lastsel);
					jQuery('#jqGridInfo').jqGrid('editRow',id,true);
					lastsel=id;
				}
			},

			editurl: "edit-fertilizer"
		});

	};
	
	module.editGrid = function(rowId){
		alert('Ngoi noi day');
		jQuery("#jqGrid").jqGrid('editRow',rowId);
		//this.disabled = 'true';
		//jQuery("#sved1,#cned1").attr("disabled",false);
	};
	module.refresh = function () { 	
		jQuery("#jqGrid").jqGrid('setGridParam',{url: 'get-fertilizers', page: 1}).trigger("reloadGrid");
	};
	
	module.deleteFertilizers = function(){
		return;
		var totalChecked = jqgrid.getSelectedRows('jqGrid');

		if (totalChecked.length == 0) {
			fancyAlert(Lang.get('common.select_items_required'), window.info_title);
			return;
		}

		module.confirm(null, null, function(){
			
			fancyAlert("Delete successfull", window.info_title);
		});
		
	};
	
	module.confirm = function(message, title, callBackFunction){

		bootbox.dialog({
			message : Lang.get('common.user_delete_confirm'),
			title : Lang.get('common.user_delete_confirm_title'),
			buttons : {
				success : {
					label : Lang.get('common.no'),
					className : "btn-primary"
				},
				danger : {
					label : Lang.get('common.yes'),
					className : "btn-primary",
					callback : function() {
						callBackFunction();
					}
				}

			}
		});
	};
	
})(fertilizerInfo = {});
$(document).ready(function(){
   $('input[name="public"]').on('click',function(){
       if($('input[name="public"]').is(":checked"))
        $("#basic").show();
       else{
           $('input[name="basis_of_calculation"]').attr('checked',false);
           $("#basic").hide();
       }
   });
});