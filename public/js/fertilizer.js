$(document).ready(function(){
    $('#hidden-select').val("");
});
$(function() {

	// Open popup
	fancyboxPopup('.fancybox-list-btn');

	$(document).on("click", ".btn-cancel-popup", function(event) {
		$.fancybox.close(true);
	});
	

});

// Delete fertilizer
$('.btn-delete-fertilizer').click(function(event){
	totalChecked = jqgrid.getIds('hidden-select');
	if (totalChecked.length == 0) {
		fancyAlert(Lang.get('common.select_min_required'), window.info_title);
		return;
	}
	

	$('#fertilizer_ids').val(totalChecked);	
	var form = $('.fertilizer-form');	
	
	gisForm.clickDeleteItems(event, {
	   formEle : form,
	   checkeds: totalChecked,
	   callbackFunction : function(data){	   	
	   		fancyMessage(data.message, window.info_title,function(){
	   			if(data.code === 1) fertilizer.refresh();
	   		});
	   }
	});
	
});

//Edit fertilizer
$('.btn-edit-fertilizer').click(function(event){
    event.preventDefault();

	totalChecked = jqgrid.getIds('hidden-select');
	if (totalChecked.length == 0) {
		fancyAlert(Lang.get('common.select_min_required'), window.info_title);
		return;
	}
	if (totalChecked.length > 1) {
		fancyAlert(Lang.get('common.select_max'), window.info_title);
		return;
	}
	
	gisForm.openPopup(window.base_url + '/edit-fertilizer/'+totalChecked);
});

//Function to specify uer for Fertilization standard
function specifyUserForFertilization(){
	totalChecked = jqgrid.getIds('hidden-select');
	if (totalChecked.length === 0) {
		fancyAlert(Lang.get('common.select_items_required'), window.info_title);
		return;
	}
	if (totalChecked.length > 1) {
		fancyAlert(Lang.get('common.select_onlyone_item_required'), window.info_title);
		return;
	}
	//do not open popup if it's system fertilization
	var status = $("#jqGrid").jqGrid("getCell", totalChecked, "created_by");
	if(status==="0"){
		fancyAlert(Lang.get('common.fertilizer_system_cannot_specify_user'), window.info_title);
		return;
	}
	gisForm.openPopup(window.base_url + '/specify-user/'+totalChecked);
}
// Copy fertilizer
$('.btn-copy-fertilizer').click(function(event){
  event.preventDefault();
	totalChecked = jqgrid.getIds('hidden-select');
	if (totalChecked.length == 0) {
		fancyAlert(Lang.get('common.select_min_required'), window.info_title);
		return;
	}
	if (totalChecked.length > 1) {
		fancyAlert(Lang.get('common.select_max'), window.info_title);
		return;
	}
    var created_by=jQuery('#jqGrid').jqGrid ('getRowData', jqgrid.getIds('hidden-select')).created_by;
    if(created_by==0){
        gisForm.openPopup(window.base_url + '/copy-system-fertilizer/'+totalChecked);

    }
    else {
        $('#hidden_fertilizer_id').val(totalChecked[0]);
        var form = $('.copy-fetilizer-frm');
        submitAjaxRequest(form, event, function (data) {
            fancyMessage(data.message, window.info_title, function () {
                if (data.code === 1) fertilizer.refresh();
            });
        });
    }

});


(function(module){

	module.model ={
		name:"TheName",
		age: 19
	};

	module.loadGridData = function(){
		$("#jqGrid")
		.jqGrid({
			url: 'get-fertilizers',
			datatype: "json",
			 colModel: [
	 			{ 
	 				label: Lang.get('common.fertilizer_standard'),
	 				name: 'fertilization_standard_name', 
	 				width: 80,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_range'),
	 				name: 'range_of_application', 
	 				width: 40,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_note'),
	 				name: 'notes', 
	 				width: 60,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_remarks'),
	 				name: 'remarks', 
	 				width: 60,
	 				sortable:false
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_initial'), 
	 				name: 'initial_display',
 				    width: 10,
 				    sortable:false,
 				    align: 'center',
	 				formatter: 'checkbox',
	 				idName : 'id',
	 				editoptions: { value: '1:0' },
                    formatoptions: { disabled: true, idName:'myid' },
					hidden:window.auth_authorization !== "1"
                    
	 			},
	 			{ 
	 				label: Lang.get('common.fertilizer_basis'), 
	 				name: 'basis_of_calculation',
 				    width: 10,
 				    sortable:false,
 				    align: 'center',
	 				formatter: 'checkbox',
	 				idName : 'id',
	 				editoptions: { value: '1:0' },
                    formatoptions: { disabled: true, idName:'myid' },
					hidden:window.auth_authorization !== "1"
                    
	 			},
                 {
                     label: Lang.get('common.fertilizer_status'),
                     name: 'not_available',
                     width: 10,
                     sortable:false,
                     align: 'center',
                     formatter: 'checkbox',
                     idName : 'id',
                     editoptions: { value: '1:0' },
                     formatoptions: { disabled: true, idName:'myid' }

                 },
	 			{
	 				name: 'created_by', 
	 				hidden:true
	 			}
	 			
		 			
			],
			autowidth: true,
            scroll: 0,
	        //jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
           	rowNum:10,
           	rowList:[10,20,30],
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			width: 'auto',
			height: 278,
			loadonce: false,	
			multiselect : true,
			pager: "#jqGridPager",
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
                    if(status==="0"){
                        $('#' + $.jgrid.jqID(rowid)).addClass('admin-standard');
                    }
                }
                
                // Update status of selection
                jqgrid.showSelection($(this),'hidden-select');
            },
            //onSelectRow: updateIdsOfSelectedRows,
            onSelectRow: function(id, isSelected){
            	jqgrid.storeId(id,isSelected,'hidden-select');
            },
            onSelectAll: function (ids, isSelected) {
            	jqgrid.storeIds(ids,isSelected,'hidden-select');
            },
            onCellSelect: function(rowId, columnId,cellcontent,e){
                var created_by=$("#jqGrid").jqGrid('getCell',rowId,'created_by');
            	if(columnId ===1){
                    if(created_by==0){
                        module.openSystemStandardCropAdmin(rowId);
                    }
            		else module.openStandardCrop(rowId);
            	}
            }
		});

	};

	module.refresh = function () { 	
		jqgrid.clearIds('hidden-select');
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
	
	module.openStandardCrop = function(standardId){		

		var flag = $('#fancy-list').val();
		if(flag !== '100'){
			$('#pager-standard-crop-list').val('');	
		}
		
		var url = window.base_url + '/standard-crops/'+standardId;
	    gisForm.openPopup(url);
	    
	    $('#fancy-list').val(''); // Clear flag
	};
	module.openSystemStandardCropAdmin = function(standardId){

		var flag = $('#fancy-list').val();
		if(flag !== '100'){
			$('#pager-standard-crop-list').val('');
		}
		var url = window.base_url + '/system-standard-crop-admin/'+standardId;
        gisForm.openPopup(url);

	    $('#fancy-list').val(''); // Clear flag
	};
	
	
})(fertilizer = {});