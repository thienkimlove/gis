// submit form
$('.btn-authorization-save').click(function(event){


	authorization.getPermission();
    gisForm.clickSave(event, {
        formEle : $('.authorization-form'),
        callbackFunction : function(data){
        	fancyAlert(data.message, window.info_title);  
        	if(data.code===1)
        	{
        		location.reload();
        	}       
        }
    });
});

(function(module){
	//'onclick'=>'authorization.saveAuthorization();'
	// submit form
//    $('.btn-forget-password').click(function(event){
//    	//return;
//        gisForm.clickSave(event, {
//            formEle : $('.authorization-form'),
//            callbackFunction : function(data){
//            	//module.refresh();
//            	fancyAlert(data.message, window.info_title);
//            }
//        });
//    });
	
	module.loadUserGroup = function(groupId){
		$('#group_id').val(groupId);
		module.getPermission();
		module.refresh();		
	};
	
	module.saveAuthorization = function(){
		var form = $('#authorization-form');	
		module.getPermission();
			
	    $.ajax({
	        type: "POST",
		    url: 'submit-authorization',
	        data: form.serialize(),
	        success: function(response) {
	        	//alert(response);
	        	fancyAlert(response.message, window.info_title);
	        }
	      });
	};
	  		
	module.getPermission = function(){
		var allRowsOnCurrentPage = $('#jqGrid').jqGrid('getDataIDs');	
		
		for (var i = 0; i < allRowsOnCurrentPage.length; i++) {
			var value =module.getCellValue('jqGrid',allRowsOnCurrentPage[i],'access');			
			$("#hidden_"+allRowsOnCurrentPage[i]).val(value==='0'?false:true);	

		}
	};
		module.loadGridData = function(){
			$("#jqGrid")
			.jqGrid({
				//url: 'get-paging-data/'+JSON.stringify(module.userModel),
				url: 'get-authorization-group/0',
				datatype: "json",
				 colModel: [
		 			{ 
		 				label: Lang.get('common.authorization_screen'),
		 				name: 'screen',
		 				width: 80,
		 				sortable:false

		 			},
		 			{ 
		 				label: Lang.get('common.authorization_access'), 
		 				name: 'access',
	 				    width: 20,
	 				    sortable:false,
	 				    align: 'center',
		 				formatter: 'checkbox',
		 				idName : 'id',
		 				editoptions: { value: '1:0' },
                        formatoptions: { disabled: false, idName:'myid' }
                        
		 			}
			 			
				],
				autowidth: true,
		        jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
               	rowNum:20,
               	rowList:[10,20,30],
				viewrecords: true, // show the current page, data rang and total records on the toolbar
				width: 600,
				height: 'auto',
				//pager: "#jqGridPager"
			});
		};

        $(window).resize(
        function() {

            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }

            window.afterResize = setTimeout(function() {
                $("#jqGrid").jqGrid('setGridWidth',
                    jQuery(".ui-jqgrid").parent().width() - 2);
            }, 500);

        });

		module.refresh = function () { 	
			var groupId =document.getElementById("group_id").value;
			if(groupId ==="") groupId =0;
			jQuery("#jqGrid").jqGrid('setGridParam',{url: 'get-authorization-group/'+groupId, page: 1}).trigger("reloadGrid");
		};

		module.getParamSelect = function(idGrid) { 	
		 	var s;
			s = jQuery("#"+idGrid).jqGrid('getGridParam','selarrrow');	
			alert(s);
			return s;			
		};

 		module.getCellValue = function(idGrid, idItem,columName){
 			var myGrid = $('#'+idGrid);
 		    var celValue = myGrid.jqGrid ('getCell', idItem, columName);
 			return celValue;
 		};
 			
	
})(authorization ={});