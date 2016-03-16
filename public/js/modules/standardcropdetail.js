

(function(module){

	module.grid = {
		isEditing : false,
		lastsel : '',
		iRow:-1,
		iCol:-1
	};

	module.loadGridData = function(){
		var grid = $("#jqGridDetail");
		var standardCropId = $('#standard_crop_id').val();
		//var lastsel='';
		$("#jqGridDetail")
		.jqGrid({
			url: 'get-standard-crop-details/'+standardCropId,
			datatype: "json",

			 colModel: [
	 			{
	 				label: Lang.get('common.standardcropdetail_nito_extraction'),
	 				name: 'nito_extraction',
	 				width: 60,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('common.standardcropdetail_nito_amount'),
	 				name: 'nito_amount',
	 				width: 60,
	 				sortable:false,
 				    align: 'right'
	 				//editable:true
	 			},
	 			{
	 				label: Lang.get('common.standardcropdetail_photpho_extraction'),
	 				name: 'photpho_extraction',
	 				width: 60,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('common.standardcropdetail_photpho_amount'),
	 				name: 'photpho_amount',
	 				index: 'photpho_amount',
	 				width: 60,
	 				sortable:false,
 				    align: 'right'
	 				//editable:true
	 			},
	 			{
	 				label: Lang.get('common.standardcropdetail_kali_extraction'),
	 				name: 'kali_extraction',
	 				width: 60,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('common.standardcropdetail_kali_amount'),
	 				name: 'kali_amount',
	 				width: 60,
	 				sortable:false,
 				    align: 'right'
	 				//editable:true
	 			},
	 			{
	 				name: 'nito_is_changed',
	 				hidden:true
	 			},
	 			{
	 				name: 'kali_is_changed',
	 				hidden:true
	 			},
	 			{
	 				name: 'photpho_is_changed',
	 				hidden:true
	 			}

			],
			autowidth: true,
            scroll: 0,
	        //jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			width: 600,
			height: 'auto',
			loadonce: false,
			scrollOffset:0,
			//cellEdit: true,
			cellsubmit: "clientArray",
            loadComplete: function() {

            	var grid = $(this);
                var ids = $(this).jqGrid("getDataIDs");
                var i, rowid;

                for (i = 0; i < ids.length; i++) {
                    rowid = ids[i];

                    var nito_status = grid.jqGrid("getCell", rowid, "nito_is_changed");
                    if(nito_status ==="true" && nito_status !== ""){
                    	tr = grid[0].rows.namedItem(rowid);
                    	td = tr.cells[1];
                    	$(td).addClass("default-detail");

                    }

                    var photpho_status = grid.jqGrid("getCell", rowid, "photpho_is_changed");
                    if(photpho_status ==="true" && photpho_status !== ""){
                    	tr = grid[0].rows.namedItem(rowid);
                    	td = tr.cells[3];
                    	$(td).addClass("default-detail");

                    }

                    var kali_status = grid.jqGrid("getCell", rowid, "kali_is_changed");
                    if(kali_status ==="true" && kali_status !== ""){
                    	tr = grid[0].rows.namedItem(rowid);
                    	td = tr.cells[5];
                    	$(td).addClass("default-detail");

                    }
                }
            },
            cellEdit: true,
        	onCellSelect : function(iRow, iCol,  cellcontent, e){
        		module.grid.iRow = iRow;
        		module.grid.iCol = iCol;

	      	      var colModel = grid.jqGrid ('getGridParam', 'colModel');
	      	      var colName = colModel[iCol].name;

	      	      var cm = grid.jqGrid('getColProp',colName);

	      	      var nitoColumn = grid.jqGrid('getColProp','nito_amount');
	      	      nitoColumn.editable = false;
	      	      var photphoColumn = grid.jqGrid('getColProp','photpho_amount');
	      	      photphoColumn.editable = false;
	      	      var kaliColumn = grid.jqGrid('getColProp','kali_amount');
	      	      kaliColumn.editable = false;

	      	      if(colName.indexOf("amount")>-1){
	                  if(cellcontent ==='&nbsp;'){
	                      cm.editable = false; // Distable editing
	                  }else{
	                	  cm.editable = true; // Enable editing
	                  }
	      	      }

        	},
        	beforeSaveCell : function(rowid,celname,value,iRow,iCol) {
        		if(value === "") return '0';
    		}
		});

		jQuery("#jqGridDetail").jqGrid('setGroupHeaders', {
			  useColSpanStyle: true,
			  groupHeaders:[
				{startColumnName: 'nito_extraction', numberOfColumns: 2, titleText: Lang.get('common.standardcropdetail_nito_group')},
				{startColumnName: 'photpho_extraction', numberOfColumns: 2, titleText: Lang.get('common.standardcropdetail_photpho_group')},
				{startColumnName: 'kali_extraction', numberOfColumns: 2, titleText: Lang.get('common.standardcropdetail_kali_group')}
			  ]
			});

	};

	module.saveGrid = function(){
		if(!module.grid.isEditing){
			$('#jqGridDetail').jqGrid('saveCell',module.grid.iRow,module.grid.iCol);

			// update value to hidden for debug
			var data = jQuery('#jqGridDetail').jqGrid('getChangedCells');
			$('#data').val(JSON.stringify(data));

			var allRowsInGrid = $('#jqGridDetail').jqGrid('getRowData');
			$('#full_data').val(JSON.stringify(allRowsInGrid));
		}
		module.grid.isEditing = false;
	};

	module.submitGrid = function(){
		module.saveGrid();

		var data = jQuery('#jqGridDetail').jqGrid('getChangedCells');
		$('#data').val(JSON.stringify(data));

		var allRowsInGrid = $('#jqGridDetail').jqGrid('getRowData');
		$('#full_data').val(JSON.stringify(allRowsInGrid));

		var form = $('.standard-crop-details-frm');
	    gisForm.clickSave(null, {
	        formEle : form,
	        callbackFunction : function(data){
	        	fancyMessage(data.message, window.info_title,
	        	function(){
	        		if(data.code=== 1) module.refresh();
	        	});
	        }
	    });
	};

	module.refresh = function () {
		var grid = $("#jqGridDetail");
		var standardCropId = $('#standard_crop_id').val();
		grid.jqGrid('setGridParam',{url: 'get-standard-crop-details/'+standardCropId, page: 1}).trigger("reloadGrid");
	};


	module.openStandardCropDetail = function(){

		totalChecked = jqgrid.getIds('pager-standard-crop-list');
		if (totalChecked.length == 0) {
			fancyAlert(Lang.get('common.select_one_item_required'), window.info_title);
			return;
		}
		if (totalChecked.length > 1) {
			fancyAlert(Lang.get('common.select_onlyone_item_required'), window.info_title);
			return;
		}

		var standardId = $('#hidden-standard-id').val();
		var standardCropId = totalChecked;
		$('#fancy-list').val('100');
		//openPopup(window.base_url + '/standard-crop-copying/'+standardCropId);
	    gisForm.openPopup(window.base_url + '/standard-crop-detail/'+standardCropId,
	    function(){
	    	fertilizer.openStandardCrop(standardId);
	    }
	    );
	};

})(standardcropdetail = {});