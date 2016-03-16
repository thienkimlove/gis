(function(module){
	
	module.setDefault = function(){
		jQuery.extend(jQuery.jgrid.defaults, { 
			//recordtext: Lang.get('common.recordtext'),
            //emptyrecords: Lang.get('common.emptyrecords'),
			//loadtext: Lang.get('common.loadtext'),
			//pgtext : Lang.get('common.pgtext'),
			loadui: 'disable'
		});
	};
	
	module.getSelectedRows = function(gridId){
		var str = jQuery("#" + gridId).jqGrid('getGridParam', 'selarrrow');
		return str;
	};

	module.getValue = function(grid, name){
        var value = grid.getGridParam('lastpage');
		return value;
	};
	
	module.controlPaging = function(grid, button){
		
        // if user has entered page number
        if (button === "user") {
            // find out the requested and last page
            //var requestedPage = $('#input_jqGridPager > input:first').val();
            var lastPage = grid.getGridParam('lastpage');

    		var pager = grid.getGridParam('pager');            
    		var requestedPage = $(pager+" :input:first").val(); 
            // if the requested page is higher than the last page value
            if (parseInt(requestedPage) > parseInt(lastPage)) {
                // set the requested page to the last page value - then reload
            	grid.trigger("reloadGrid", [{page: lastPage}]);
            };
        };
	};

	module.storePage = function (page, hiddenId) {
		var string = $('#'+hiddenId).val();
		var idsOfSelectedRows = string.split(",");
		
		idsOfSelectedRows.splice(0, 1); // remove currentPage from the list
			    
	    idsOfSelectedRows.unshift(page); // insert item to the beginning of array

	    $('#'+hiddenId).val(idsOfSelectedRows);
	};
	
	module.storeId = function (id, isSelected, hiddenId) {
		var string = $('#'+hiddenId).val();
		var idsOfSelectedRows = string.split(",");
		
		var currentPage = idsOfSelectedRows[0]!==""?idsOfSelectedRows[0]:1;
		idsOfSelectedRows.splice(0, 1); // remove currentPage from the list
		
	    var index = $.inArray(id, idsOfSelectedRows);
	    if (!isSelected && index >= 0) {
	        idsOfSelectedRows.splice(index, 1); // remove id from the list
	    } else if (index < 0) {
	        idsOfSelectedRows.push(id);
	    }
	    
	    idsOfSelectedRows.unshift(currentPage); // insert item to the beginning of array

	    $('#'+hiddenId).val(idsOfSelectedRows);
	};

	module.storeIds = function (ids, isSelected, hiddenId) {
        for (var i = 0; i < ids.length; i++) {
            var id = ids[i];
            module.storeId(id, isSelected,hiddenId);
        };
	};
	
	module.getIds = function(hiddenId){
		var string = $('#'+hiddenId).val();
		idsOfSelectedRows = string.split(",");
		idsOfSelectedRows.splice(0, 1); // remove currentPage from the list
		return idsOfSelectedRows;
	};

	module.clearIds = function(hiddenId){
		$('#'+hiddenId).val('');
	};
	
	module.showSelection = function(grid,hiddenId){
	    var idsOfSelectedRows = module.getIds(hiddenId);
	    for (var i = 0; i < idsOfSelectedRows.length; i++) {
	    	grid.jqGrid('setSelection', idsOfSelectedRows[i], false);
	    }

		var pager = grid.getGridParam('page');
		module.storePage(pager,hiddenId);
	};

	module.setPager = function(grid, hiddenId){

		var lastpage = $(this).getGridParam('lastpage');
		if(lastpage ===0){
			var string = $('#'+hiddenId).val();
			var idsOfSelectedRows = string.split(",");
			
			var currentPage = idsOfSelectedRows[0]!==""?idsOfSelectedRows[0]:1;
			grid.jqGrid('setGridParam', { postData: { page: currentPage,}});
		}		
	};
	
	
})(jqgrid = {});