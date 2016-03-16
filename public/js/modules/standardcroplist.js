$(document).ready(function(){
    $('.ui-jqgrid-sortable').css({cursor:"default"});
    $('.ui-state-default').css({color: "#ffffff !important"});
});
// Copy Standard crop
$('.btn-copy-standard-crop').click(function(event){  
	event.preventDefault();
	
	standardcroplist.openStandardCropCopying();
	

});
// Delete Standard crops
$('.btn-delete-standard-crops').click(function(event){  
	event.preventDefault();
	
	totalChecked = jqgrid.getIds('pager-standard-crop-list');
    if (totalChecked.length === 0) {
        fancyAlert(Lang.get('common.select_min_required'), window.info_title);
        return;
    }
    if (totalChecked.length > 1) {
        fancyAlert(Lang.get('common.select_max'), window.info_title);
        return;
    }
	
	$('#standard_crop_ids').val(totalChecked);
	var form=  $('.standard-crops-frm');	
	gisForm.clickDeleteItems(event, {
	   formEle : form,
	   checkeds: totalChecked,
	   callbackFunction : function(data){	   	
	   		fancyMessage(data.message, window.info_title,function(){
	   			if(data.code === 1) standardcroplist.refresh();
	   		});   
	   }
	});

});


// Add Standard crop
$('.btn-add-standard-crop').click(function(event){  
	event.preventDefault();
	var standardId = $('#hidden-standard-id').val();
	standardcroplist.openStandardCropInfo(standardId);
});


// Delete Standard crops
$('.btn-edit-standard-crop').click(function(event){  
	event.preventDefault();
	
	totalChecked = jqgrid.getIds('pager-standard-crop-list');
	if (totalChecked.length === 0) {
		fancyAlert(Lang.get('common.select_min_required'), window.info_title);
		return;
	}
	if (totalChecked.length > 1) {
		fancyAlert(Lang.get('common.select_max'), window.info_title);
		return;
	}
	var standardId = $('#hidden-standard-id').val();
	standardcroplist.editStandardCropInfo(totalChecked[0],standardId);
});


// opend Standard crop detail
$('.btn-detail-standard-crop').click(function(event){  	
	standardcroplist.openStandardCropDetail();
});


(function(module){

	module.loadGridData = function(){
		var standardId = $('#hidden-standard-id').val();
		$("#jqGridInfo")
		.jqGrid({
			url: 'get-standard-crops/'+standardId,
			datatype: "json",
			 colModel: [
	 			{ 
	 				label: Lang.get('common.standardcrop_crop'),
	 				//label: 'crops_name',
	 				name: 'crop.crops_name', 
	 				width: 80,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.standardcrop_n'),
	 				//label: 'Nito',
	 				name: 'fertilization_standard_amount_n', 
	 				width: 40,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.standardcrop_p'),
	 				//label: 'Photpho',
	 				name: 'fertilization_standard_amount_p', 
	 				width: 60,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.standardcrop_k'),
	 				//label: 'Kali',
	 				name: 'fertilization_standard_amount_k', 
	 				width: 60,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.standardcrop_not_avarible'), 
	 				name: 'not_available',
 				    width: 30,
 				    sortable:false,
 				    align: 'center',
	 				formatter: 'checkbox',
	 				idName : 'id',
	 				editoptions: { value: '1:0' },
                    formatoptions: { disabled: true, idName:'myid' }
                    
	 			},
	 			{ 
	 				label: Lang.get('common.standardcrop_remarks'),

	 				name: 'remarks', 
	 				width: 60,
	 				sortable:false
	 			} 			
		 			
			],
			autowidth: true,
            scroll: 0,
	        //jsonReader: { repeatitems: false, id: "id" }, // Change identify column, default 'id'
           	rowNum:10,
           	rowList:[10,20,30],
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			width: 600,
			height: 278,
			loadonce: false,	
			multiselect : true,
			scrollOffset:0,
			pager: "#jqGridPagerInfo",
			beforeRequest: function(){				
				jqgrid.setPager($(this),'pager-standard-crop-list');
			},
            onPaging : function(pgButton){
            	jqgrid.controlPaging($(this),pgButton);
            }, 
            loadComplete: function() {
            	
            	// Update status of create_by
                var ids = $(this).jqGrid("getDataIDs");
                var i, rowid, status;
                
                for (i = 0; i < ids.length; i++) {
                    rowid = ids[i];
                    // get data from some column "readStatus"
                    status = $(this).jqGrid("getCell", rowid, "created_by");
                    if(status==="true"){
                        $('#' + $.jgrid.jqID(rowid)).addClass('admin-standard');
                    }
                }
                
                // Update status of selection
                jqgrid.showSelection($(this),'pager-standard-crop-list');
            },
            //onSelectRow: updateIdsOfSelectedRows,
            onSelectRow: function(id, isSelected){
            	jqgrid.storeId(id,isSelected,'pager-standard-crop-list');
            },
            onSelectAll: function (ids, isSelected) {
            	jqgrid.storeIds(ids,isSelected,'pager-standard-crop-list');
            }
		});
		
	};
	
	module.refresh = function () { 	
		jqgrid.clearIds('pager-standard-crop-list');
		var standardId = $('#hidden-standard-id').val();
		jQuery("#jqGridInfo").jqGrid('setGridParam',{url: 'get-standard-crops/'+standardId, page: 1}).trigger("reloadGrid");
	};
	
	module.deleteFertilizers = function(){
		
	};

	module.openStandardCropInfo = function(standardId){	
		
		$('#fancy-list').val('100');
	    gisForm.openPopup(window.base_url + '/standard-crop-info/'+standardId,
	    function(){
	    	fertilizer.openStandardCrop(standardId);
	    }
	    );
	};

	module.editStandardCropInfo = function(standardCropId,standardId){	
		
		$('#fancy-list').val('100');
	    gisForm.openPopup(window.base_url + '/edit-standard-crop/'+standardCropId,
	    function(){
	    	fertilizer.openStandardCrop(standardId);
	    }
	    );
	};

	module.openStandardCropCopying = function(){	

		totalChecked = jqgrid.getIds('pager-standard-crop-list');
		if (totalChecked.length == 0) {
			fancyAlert(Lang.get('common.select_min_required'), window.info_title);
			return;
		}
		if (totalChecked.length > 1) {
			fancyAlert(Lang.get('common.select_max'), window.info_title);
			return;
		}

		var standardId = $('#hidden-standard-id').val();
		var standardCropId = totalChecked;
		$('#fancy-list').val('100');
		//openPopup(window.base_url + '/standard-crop-copying/'+standardCropId);
	    gisForm.openPopup(window.base_url + '/standard-crop-copying/'+standardCropId,
	    function(){
	    	fertilizer.openStandardCrop(standardId);
	    }
	    );
	};

	module.openStandardCropDetail = function(){	

		totalChecked = jqgrid.getIds('pager-standard-crop-list');
		if (totalChecked.length == 0) {
			fancyAlert(Lang.get('common.select_min_required'), window.info_title);
			return;
		}
		if (totalChecked.length > 1) {
			fancyAlert(Lang.get('common.select_max'), window.info_title);
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
	
})(standardcroplist = {});