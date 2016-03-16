(function(module){	
	module.specifyUserModel = {
		fertilizer_id:'',
		user_code:'',
		username:'',
		group_id:''
	};

	module.loadGridData = function(){
		module.setSearchingModel();
		$("#jqGridInfo")
		.jqGrid({
			//url: 'get-paging-data/'+JSON.stringify(module.userModel),
			url: 'get-specify-users/'+JSON.stringify(module.specifyUserModel),
			datatype: "json",
			 colModel: [
	 			{ 
	 				label: Lang.get('common.specifyuser_user_code'),
	 				//label: Lang.get('User code'),
	 				name: 'user_code', 
	 				width: 80,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.specifyuser_user_name'),
	 				//label: Lang.get('Username'),
	 				name: 'username', 
	 				width: 80,
	 				sortable:false, 
	 				editable:true
	 			},
	 			{ 
	 				label: Lang.get('common.specifyuser_user_group'),
	 				//label: Lang.get('Group'),
	 				name: 'usergroup.group_name', 
	 				width: 80,
	 				sortable:false,
	 				editable:true,
	 				
	 			}
		 			
			],
			autowidth: true,
            scroll: 0,
	        jsonReader: { repeatitems: false, id: "user_code" }, // Change identify column, default 'id' 
           	rowNum:10,
           	rowList:[10,20,30],
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			width: 'auto',
			height: 278,
			loadonce: false,	
			multiselect : true,
			scrollOffset:8,
	        pager : "#jqGridPagerInfo", 
            onPaging : function(pgButton){
            	jqgrid.controlPaging($(this),pgButton);
            },
            onSelectRow: function(id, isSelected){
            	jqgrid.storeId(id,isSelected,'hidden-select-info');
            },
            onSelectAll: function (ids, isSelected) {
            	jqgrid.storeIds(ids,isSelected,'hidden-select-info');
            },
            loadComplete: function() {
                jqgrid.showSelection($(this),'hidden-select-info');
            },
		});

		//resizeGrid('jqGridInfo');
	};

	module.setSearchingModel = function(){
		
		module.specifyUserModel.fertilizer_id = $('#fertilizer-id').val();
		module.specifyUserModel.user_code = $('#specifyuser_user_code').val();
		module.specifyUserModel.username = $('#specifyuser_user_name').val();
		module.specifyUserModel.group_id = $('#specifyuser_group_id').val();
	};	
	module.refresh = function () { 	
		module.setSearchingModel();
		$('#hidden-select-info').val(window.standardUserCodes);		
		jQuery("#jqGridInfo").jqGrid('setGridParam',{url:'get-specify-users/'+JSON.stringify(module.specifyUserModel), page: 1}).trigger("reloadGrid")
	};

	module.saveSpecifyUser = function () { 	

	    var selectedIds = jqgrid.getIds('hidden-select-info');
	    $('#selected_ids').val(selectedIds);
	    gisForm.clickSave(null, {    	
	        formEle : $('.specify-user-frm'),
	        callbackFunction : function(data){
	        	fancyMessage(data.message, window.info_title,
	        	function(){
	        		if(data.code=== 1) {
	        			closeFancy();
	        			fertilizer.refresh();
	        		}
	        	});     
	        }
	    });
	};
	
})(specifyuser = {});