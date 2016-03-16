/**
 fertilizer_table in resource/view/demo/map.blade.php
 */

(function(module){

	module.loadGridData1 = function(){

		var mydata = [{
			column1: "Standard 1",
			column2: "1",
			column3: "2",
			column4: "3"
		}];

		$("#jqGrid1")
		.jqGrid({
	        data: mydata,
	        datatype: "local",
			//url: 'get-fertilizers',
			//datatype: "json",
			 colModel: [
	 			{
	 				label: Lang.get('Fertilizers'),
	 				name: 'column1',
	 				width: 200,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Nito'),
	 				name: 'column2',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Photpho'),
	 				name: 'column3',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Kali'),
	 				name: 'column4',
 				    align: 'right',
	 				width: 100,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Price'),
	 				name: 'column5',
 				    align: 'right',
	 				width: 100,
	 				sortable:false
	 			},


			],
			autowidth: true,
            scroll: 0,
	        //jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
           	rowNum:1,
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			loadonce: false,
			scrollOffset:0,
			width: 'auto',
			height: 'auto',

		});
	};


	module.loadGridData2 = function(){

		var mydata = [{
			column0: "Type 1",
			column1: "phan bon A",
			column2: "1",
			column3: "2",
			column4: "3"
		},
		{
			column0: "Type 2",
			column1: "phan bon B",
			column2: "3",
			column3: "4",
			column4: "5"
		}];

		$("#jqGrid2")
		.jqGrid({
	        data: mydata,
	        datatype: "local",
			//url: 'get-fertilizers',
			//datatype: "json",
			 colModel: [
	 			{
	 				label: Lang.get('Type'),
	 				name: 'column0',
	 				width: 100,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Fertilizers'),
	 				name: 'column1',
	 				width: 200,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Nito'),
	 				name: 'column2',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Photpho'),
	 				name: 'column3',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Kali'),
	 				name: 'column4',
 				    align: 'right',
	 				width: 100,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Price'),
	 				name: 'column5',
 				    align: 'right',
	 				width: 100,
	 				sortable:false
	 			},


			],
				autowidth: true,
				scroll: 0,
				//jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
				rowNum:2,
				viewrecords: true, // show the current page, data rang and total records on the toolbar
				loadonce: false,
				scrollOffset:0,
				width: 'auto',
				height: 'auto',

		});
	};

	module.displayButton = function(cellvalue, options, rowObject)
	{
		switch(options.rowId) {
	    case "1":
		    var button = "<input class='button-submit' type='button' value='Edit' onclick=\"openDialog('/admin/organicmatter/byproduct',600,450);\"  />";
		    return button;
	        break;
	    case "2":
		    var button = "<input class='button-submit' type='button' value='Edit' onclick=\"openDialog('/admin/organicmatter/greenmanure',900,450);\"  />";
		    return button;
	        break;
	    case "3":
		    var button = "<input class='button-submit' type='button' value='Edit' onclick=\"openDialog('/admin/organicmatter/compost',600,400);\"  />";
		    return button;
	        break;
	    case "5":
		    var button = "<input class='button-submit' type='button' value='Execute' onclick=\"\"  />";
		    return button;
	        break;
	    default:
	        return "";
		}

	};

	module.loadGridData3 = function(){

		var mydata = [{
			column1: "Option 1",
			column2: "1",
			column3: "2",
			column4: "3",
		},
		{
			column1: "Option 2",
			column2: "3",
			column3: "4",
			column4: "5",
		},
		{
			column1: "Option 3",
			column2: "3",
			column3: "4",
			column4: "5",
		},
		{
			column1: "Option 4",
			column2: "3",
			column3: "4",
			column4: "5",
		},
		{
			column1: "Option 5",
			column2: "3",
			column3: "4",
			column4: "5",
		}];

		$("#jqGrid3")
		.jqGrid({
	        data: mydata,
	        datatype: "local",
			//url: 'get-fertilizers',
			//datatype: "json",
			 colModel: [
	 			{
	 				label: Lang.get('Fertilizers'),
	 				name: 'column1',
	 				width: 200,
	 				sortable:false,
	 			},
	 			{
	 				label: Lang.get('Nito'),
	 				name: 'column2',
	 				width: 100,
 				    align: 'right',
	 				sortable:false,
	 			},
	 			{
	 				label: Lang.get('Photpho'),
	 				name: 'column3',
	 				width: 100,
 				    align: 'right',
	 				sortable:false,
	 			},
	 			{
	 				label: Lang.get('Kali'),
	 				name: 'column4',
 				    align: 'right',
	 				width: 100,
	 				sortable:false,
	 			},
	 			{
	 				name:'',
	 				sortable:false,
	 				formatter: module.displayButton,
 				    align: 'center',
	 				width: 100,
	 				sortable:false,
	 			}


			],
			autowidth: true,
            scroll: 0,
	        //jsonReader: { repeatitems: false, id: "code" }, // Change identify column, default 'id'
           	rowNum:5,
			viewrecords: true, // show the current page, data rang and total records on the toolbar
			loadonce: false,
			scrollOffset:0,
			width: 'auto',
			height: 'auto',

		});
	};

	module.loadGridData4 = function(){

		var mydata = [{
			column1: "Fertilizer A",
			column2: "1",
			column3: "2",
			column4: "3"
		},
		{
			column1: "Fertilizer B",
			column2: "3",
			column3: "4",
			column4: "5"
		},
		{
			column1: "Fertilizer C",
			column2: "3",
			column3: "4",
			column4: "5"
		},
		{
			column1: "Fertilizer wwD",
			column2: "3",
			column3: "4",
			column4: "5"
		},
		{
			column1: "Fertilizer D",
			column2: "3",
			column3: "4",
			column4: "5"
		}];

		$("#jqGrid4")
		.jqGrid({
	        data: mydata,
	        datatype: "local",
			//url: 'get-fertilizers',
			//datatype: "json",
			 colModel: [
	 			{
	 				label: Lang.get('Fertilizers'),
	 				name: 'column1',
	 				width: 200,
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Nito'),
	 				name: 'column2',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Photpho'),
	 				name: 'column3',
	 				width: 100,
 				    align: 'right',
	 				sortable:false
	 			},
	 			{
	 				label: Lang.get('Kali'),
	 				name: 'column4',
 				    align: 'right',
	 				width: 100,
	 				sortable:false
	 			},


			],

			autowidth: true,
            scroll: false,
           	rowNum:4,
			scrollOffset:8,
				editurl: 'clientArray',
			pager: "#jqGridPager4"

		});

		$('#jqGrid4').navGrid("#jqGridPager4", {edit: false, add: false, del: false, refresh: false, view: false});
		$('#jqGrid4').inlineNav('#jqGridPager4',
			// the buttons to appear on the toolbar of the grid
			{
				edit: true,
				add: true,
				del: true,
				cancel: true,
				editParams: {
					keys: true,
				},
				addParams: {
					keys: true
				}
			});
	};
	module.refresh = function () {
		jqgrid.clearIds('hidden-select');
		jQuery("#jqGrid").jqGrid('setGridParam',{url: 'get-fertilizers', page: 1}).trigger("reloadGrid");
	};


	module.initPage = function () {
	    creatingmap.loadGridData1();
	    creatingmap.loadGridData2();
	    creatingmap.loadGridData3();
	    creatingmap.loadGridData4();

		var mydata = [{
			column1: "Fertilizer A",
			column2: "1",
			column3: "2",
			column4: "3"
		},
			{
				column1: "Fertilizer B",
				column2: "3",
				column3: "4",
				column4: "5"
			},
			{
				column1: "Fertilizer D",
				column2: "3",
				column3: "4",
				column4: "5"
			}];

		$("#43rowed3").jqGrid({
			url: 'get-fertilizers',
			data: mydata,
			datatype: "local",
			colNames:['Inv No','Date', 'Client', 'Amount','Tax','Total','Notes'],
			colModel:[
				{name:'id',index:'id', width:55},
				{name:'invdate',index:'invdate', width:90, editable:true},
				{name:'name',index:'name', width:100,editable:true},
				{name:'amount',index:'amount', width:80, align:"right",editable:true},
				{name:'tax',index:'tax', width:80, align:"right",editable:true},
				{name:'total',index:'total', width:80,align:"right",editable:true},
				{name:'note',index:'note', width:150, sortable:false,editable:true}
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: '#p43rowed3',
			sortname: 'id',
			viewrecords: true,
			sortorder: "desc",
			caption: "Using navigator"
		});
		//jQuery("#43rowed3").jqGrid('navGrid',"#p43rowed3",{edit:false,add:false,del:false});
		//jQuery("#43rowed3").jqGrid('inlineNav',"#p43rowed3");
	};

	module.openCreatingMap = function (code1, code2, code3) {
	    gisForm.openPopup(window.base_url + '/creating-map/'+code1+'/'+code2+'/'+code3,
    	    function(){
    	    	//fertilizer.openStandardCrop(standardId);
    	    }
	    );
	};


})(creatingmap = {});


var lastSelection;

function editRow(id) {
	if (id && id !== lastSelection) {
		var grid = $("#jqGrid");
		grid.jqGrid('restoreRow',lastSelection);
		grid.jqGrid('editRow',id, {keys: true} );
		lastSelection = id;
	}
}


$(document).ready(function () {
	return;
	var template = "<div style='margin-left:15px;'><div>Loai_phan_bon<sup>*</sup>:</div><div> {column1} </div>";
	template += "<div>nito</div><div>{column2} </div>";
	template += "<div>photpho</div><div>{column3}</div>";
	template += "<div>kali</div><div> {column4} </div>";
	template += "<hr style='width:100%;'/>";
	template += "<div> {sData} {cData}  </div></div>";

	var mydata = [{
		column1: "phan bon A",
		column2: "1",
		column3: "2",
		column4: "3"
	},
		{
			column1: "phan bon B",
			column2: "3",
			column3: "4",
			column4: "5"
		}];
	//Data vi du

	jQuery("#jqGrid").jqGrid({

		data: mydata,
		//url:'demodata.json',
		editurl:'clientArray',//thay doi phia client
		datatype: "local",
		//datatype:"json",


		colModel : [{
			label : Lang.get('common.fertilizer_title'),
			name : 'column1',
			width: 150,
			key:true,
			editable: true,
			editrules:{ required: true}
		},{
			label : Lang.get('common.standardcrop_n'),
			name : 'column2',
			width : 75,
			editable:true
		},{
			label : Lang.get('common.standardcrop_p'),
			name : 'column3',
			width : 75,
			editable:true
		},{
			label : Lang.get('common.standardcrop_k'),
			name : 'column4',
			width : 75,
			editable:true
		},],
		sortname:Lang.get('common.standardcrop_n'),
		sortorder:'asc',
		loadonce:true,
		viewrecords:true,
		width:500,
		height:200,
		rowNum:12,
		pager: "#jqGridPager"
	});

	$('#jqGrid').navGrid('#jqGridPager',
		// the buttons to appear on the toolbar of the grid
		{ edit: true, add: true, del: true, search: false, refresh: false, view: false, position: "left", cloneToTop: false },
		// options for the Edit Dialog
		{
			editCaption: "The Edit Dialog",
			template: template,
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText;
			}
		},
		// options for the Add Dialog
		{
			template: template,
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText;
			}
		},
		// options for the Delete Dailog
		{
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText;
			}
		});
});