(function(module){
    module.isEditing = false;
    module.isEdited = false;
    module.grid1 = {
        isEditing : false,
        iRow:-1,
        iCol:-1
    };
    module.grid2 = {
        isEditing : false,
        iRow:-1,
        iCol:-1
    };
    module.grid3 = {
        isEditing : false,
        iRow:-1,
        iCol:-1
    };
    module.grid4 = {
        isEditing : false,
        iRow:-1,
        iCol:-1
    };

    module.setEditing = function(){
        module.isEditing = true;
        module.isEdited = false;
    };

    module.setEdited = function(){
        if(module.isEdited) return;
        if(module.isEditing === false){
            module.saveGrids();
            module.isEdited = true;
        }
        module.isEditing = false;
    };
    module.loadGridData1 = function(){

        var defaultData = [{
            one_barrel_fertilizer_name: "",
            one_barrel_n: "",
            one_barrel_p: "",
            one_barrel_k: "",
            fertilizer_type:"",
            fertilizer_price: ""
        }];
		
		if(typeof $('#init-data').val() != 'undefined'){
			var initData = JSON.parse($('#init-data').val());
			var dataSave = initData.fertilizing_machine_type == 1 ? initData.machine : defaultData;
		}
		var mydata = (typeof dataSave == 'undefined') ? defaultData : dataSave;
        $("#jqGrid1")
            .jqGrid({
                data: mydata,
                datatype: "local",
                autoencode: true,
                colModel: [
                    {
                        label: Lang.get('common.creating_map_table_1_column1'),
                        name: 'one_barrel_fertilizer_name',
                        width: 200,
                        editable: true,
                        classes: 'text200',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_1_column2'),
                        name: 'one_barrel_n',
                        width: 100,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_1_column3'),
                        name: 'one_barrel_p',
                        width: 100,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_1_column4'),
                        name: 'one_barrel_k',
                        align: 'right',
                        width: 100,
                        editable: true,
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_1_column6'),
                        name: 'fertilizer_price',
                        align: 'right',
                        width: 100,
                        editable: true,
                        classes: 'onlyNumeric9',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_1_column5'),
                        name: 'fertilizer_type',
                        align: 'right',
                        width: 100,
                        editable: true,
                        edittype: "select",
                        sortable:false,
                        editoptions: {
                            value: "1:20;2:500"
                        }
                    }
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
                cellEdit: true,
                cellsubmit: "clientArray",
                onCellSelect : function(iRow, iCol,  cellcontent, e){
                },
                beforeEditCell: function(rowid, cellname, value, iRow, iCol	){
                    module.grid1.iRow = iRow;
                    module.grid1.iCol = iCol;
                }

            });

    };
    module.loadGridData2 = function(){
        var defaultData = [
			{
				barrel: Lang.get('common.creating_map_table_2_row2'),
				fertilizer_name: "",
				fertilizer_n: '',
				fertilizer_p: '',
				fertilizer_k: '',
				fertilizer_type: "",
				fertilizer_price: ''
			},
            {
                barrel: Lang.get('common.creating_map_table_2_row3'),
                fertilizer_name: "",
				fertilizer_n: '',
				fertilizer_p: '',
				fertilizer_k: '',
				fertilizer_type: "",
				fertilizer_price: ''
            }
		];
		
		if(typeof $('#init-data').val() != 'undefined'){
			var initData = JSON.parse($('#init-data').val());
			var dataSave = initData.fertilizing_machine_type == 1 ? defaultData : initData.machine;
		}
		var mydata = (typeof dataSave == 'undefined') ? defaultData : dataSave;
        var grid = $("#jqGrid2");
        $("#jqGrid2")
            .jqGrid({
                data: mydata,
                datatype: "local",
                autoencode: true,
                //url: 'get-fertilizers',
                //datatype: "json",
                colModel: [
                    {
                        label: Lang.get('common.creating_map_table_2_column1'),
                        name: 'barrel',
                        width: 120,
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column2'),
                        name: 'fertilizer_name',
                        width: 200,
                        editable: true,
                        classes: 'text200',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column3'),
                        name: 'fertilizer_n',
                        width: 130,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column4'),
                        name: 'fertilizer_p',
                        width: 140,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column5'),
                        name: 'fertilizer_k',
                        align: 'right',
                        width: 130,
                        editable: true,
                        classes: 'onlyDecimal3_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column7'),
                        name: 'fertilizer_price',
                        align: 'right',
                        width: 120,
                        editable: true,
                        classes: 'onlyNumeric9',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_2_column6'),
                        name: 'fertilizer_type',
                        align: 'right',
                        width: 140,
                        editable: true,
                        edittype: "select",
                        sortable:false,
                        editoptions: {
                            value: "1:20;2:500"
                        }
                    }
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
                cellEdit: true,
                cellsubmit: "clientArray",
                onCellSelect : function(iRow, iCol,  cellcontent, e){
                },
                beforeEditCell: function(rowid, cellname, value, iRow, iCol	){
                    module.grid2.iRow = iRow;
                    module.grid2.iCol = iCol;
                }
            });
    };
    module.loadGridData3 = function(){
        var defaultData = [
			{
				organic_matter_field_type: Lang.get('common.creating_map_table_3_row1'),
				type:"1"
			},
            {
                organic_matter_field_type: Lang.get('common.creating_map_table_3_row2'),
                type:"2"
            },
            {
                organic_matter_field_type: Lang.get('common.creating_map_table_3_row3'),
                type:"3"
            },
            {
                organic_matter_field_type: Lang.get('common.creating_map_table_3_row4'),
                type:"4"
            },
            {
                organic_matter_field_type: Lang.get('common.creating_map_table_3_row5'),
                type:"5"
            }
		];
		if(typeof $('#init-data').val() != 'undefined'){
			var initData = JSON.parse($('#init-data').val());
			var dataSave = initData.organic_matter;
		}
		var mydata = (typeof dataSave == 'undefined') ? defaultData : dataSave;
        var grid = $("#jqGrid3");
        $("#jqGrid3")
            .jqGrid({
                data: mydata,
                datatype: "local",
                //url: 'get-fertilizers',
                //datatype: "json",
                colModel: [
                    {
                        label: Lang.get('common.creating_map_table_3_column1'),
                        name: 'organic_matter_field_type',
                        width: 150,
                        classes: 'text200',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_3_column2'),
                        name: 'n',
                        width: 100,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal4_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_3_column3'),
                        name: 'p',
                        width: 100,
                        editable: true,
                        align: 'right',
                        classes: 'onlyDecimal4_1',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_3_column4'),
                        name: 'k',
                        align: 'right',
                        width: 100,
                        editable: true,
                        classes: 'onlyDecimal4_1',
                        sortable:false
                    },
                    {
                        name: Lang.get('common.creating_map_table_3_column5'),
                        sortable:false,
                        formatter: module.displayButton,
                        align: 'center',
                        width: 100,
                        sortable:false
                    },
                    {
                        name: 'type',
                        hidden:true
                    },


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
                cellEdit: true,
                cellsubmit: "clientArray",
                loadComplete : function(){
                    creatingmap.sumTable3();
                },
                onCellSelect : function(iRow, iCol,  cellcontent, e){
                    var isEdit = true;
                    if(iRow=="5" || iCol === 4|| iCol ===0) isEdit = false;
                    module.grid3.iRow = iRow;
                    module.grid3.iCol = iCol;

                    var colModel = grid.jqGrid ('getGridParam', 'colModel');
                    var colName = colModel[iCol].name;

                    var cm = grid.jqGrid('getColProp',colName);
                    cm.editable = isEdit; // Enable editing
                },
                afterSaveCell: function(event){
                    creatingmap.sumTable3();
                },
                beforeEditCell: function(rowid, cellname, value, iRow, iCol	){
                    module.grid3.iRow = iRow;
                    module.grid3.iCol = iCol;
                }

            });
    };
    module.loadGridData4 = function(){
	
        var defaultData = [{
            fertilization_stage: "",
            n: "",
            p: "",
            k: ""
        },
        {
            fertilization_stage: "",
            n: "",
            p: "",
            k: ""
        },
        {
            fertilization_stage: "",
            n: "",
            p: "",
            k: ""
        },
        {
            fertilization_stage: "",
            n: "",
            p: "",
            k: ""
        },
        {
           fertilization_stage: "",
            n: "",
            p: "",
            k: ""
        }];
		
		if(typeof $('#init-data').val() != 'undefined'){
			var initData = JSON.parse($('#init-data').val());
			var dataSave = initData.stages;
		}
		var mydata = (typeof dataSave == 'undefined') ? defaultData : dataSave;
		
        var grid = $("#jqGrid4");
        $("#jqGrid4")
            .jqGrid({
                data: mydata,
                datatype: "local",
                autoencode: true,
                editurl: 'clientArray',
                colModel: [
                    {
                        label: Lang.get('common.creating_map_table_4_column1'),
                        name: 'fertilization_stage',
                        width: 200,
                        editable:false,
                        classes: 'text200',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_4_column2'),
                        name: 'n',
                        width: 100,
                        align: 'right',
                        editable:true,
                        classes: 'grid-int4',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_4_column3'),
                        name: 'p',
                        width: 100,
                        align: 'right',
                        editable:true,
                        classes: 'grid-int4',
                        sortable:false
                    },
                    {
                        label: Lang.get('common.creating_map_table_4_column4'),
                        name: 'k',
                        align: 'right',
                        width: 100,
                        editable:true,
                        classes: 'grid-int4',
                        sortable:false
                    }
                ],

                autowidth: true,
                autoheight: true,
                loadonce:false,
                scroll: 0,
                scrollOffset:17,
                rowNum:5,
                footerrow: true,
                cellEdit: true,
                cellsubmit: "clientArray",
                loadComplete: function() {
                    creatingmap.sumTable4();
                },
                onCellSelect : function(iRow, iCol){
                    var isEdit = true;
                    module.grid4.iRow = iRow;
                    module.grid4.iCol = iCol;
                    var colModel = grid.jqGrid ('getGridParam', 'colModel');
                    var colName = colModel[iCol].name;
                    var cm = grid.jqGrid('getColProp',colName);
                    cm.editable = isEdit; // Enable editing
                },
                afterSaveCell: function(event){
                    creatingmap.sumTable4();
                },
                beforeEditCell: function(rowid, cellname, value, iRow, iCol	){
                    module.grid4.iRow = iRow;
                    module.grid4.iCol = iCol;
                }

            });
    };
    module.displayButton = function(cellvalue, options, rowObject){
        switch(options.rowId) {
            case "1":
                var button = "<input class='button-submit mark-tab-nito' type='button' value="+Lang.get('common.creating_fertilizer_map_button_edit')+" onclick=\"openDialog('/admin/organicmatter/byproduct',500,470);\"  />";
                return button;
                break;
            case "2":
                var button = "<input class='button-submit' type='button' value="+Lang.get('common.creating_fertilizer_map_button_edit')+" onclick=\"openDialog('/admin/organicmatter/greenmanure',500,535);\"  />";
                return button;
                break;
            case "3":
                var button = "<input class='button-submit' type='button' value="+Lang.get('common.creating_fertilizer_map_button_edit')+" onclick=\"openDialog('/admin/organicmatter/compost',500,755);\"  />";
                return button;
                break;
            default:
                return "";
        }

    };
    module.initPage = function () {
        loadGridTable.loadGridData1();
        loadGridTable.loadGridData2();
        loadGridTable.loadGridData3();
        loadGridTable.loadGridData4();
    };
    module.saveGrids = function(){
        $('#jqGrid1').jqGrid('saveCell',module.grid1.iRow,module.grid1.iCol);
        $('#jqGrid2').jqGrid('saveCell',module.grid2.iRow,module.grid2.iCol);
        $('#jqGrid3').jqGrid('saveCell',module.grid3.iRow,module.grid3.iCol);
        $('#jqGrid4').jqGrid('saveCell',module.grid4.iRow,module.grid4.iCol);
        var data3 = jQuery('#jqGrid3').jqGrid('getRowData');
        for (var i = 0; i < data3.length; i++) {
            delete data3[i].button;
            delete data3[i].organic_matter_field_type;
        }
        $('#table3').val(JSON.stringify(data3));
        var data4 = jQuery('#jqGrid4').jqGrid('getRowData');
        var array =[];
        for (var i = data4.length-1; i >=0; i--) {
            if(data4[i].name+data4[i].n+data4[i].p+data4[i].k !==""){
                array.push(data4[i]);
            }
        }
        $('#table4').val(JSON.stringify(array));

    };

    module.saveClientData = function(){
        var control_methodology = $("input:radio[name='control_methodology']:checked" ).val();
        var fixed_fertilizer_amount_name = 'fixed_fertilizer_amount'+control_methodology;
        var fixed_fertilizer_amount = $("#"+fixed_fertilizer_amount_name).val();
        $("[name='fixed_fertilizer_amount']").val(fixed_fertilizer_amount);
        var cropName = $("#crops_id option:selected").text();
        $("#crop_name").val(cropName);
        var fertilizing_machine_type = $("input:radio[name='fertilizing_machine_type']:checked" ).val();
        if(fertilizing_machine_type === "1"){
            //for 1 barrel
            var rowData = $("#jqGrid1").getRowData(1);
            if(rowData.fertilizer_type === '20') rowData.fertilizer_type =1;
            if(rowData.fertilizer_type === '500') rowData.fertilizer_type =2;

            $("#one_barrel_fertilizer_name").val(rowData.one_barrel_fertilizer_name);
            $("#one_barrel_n").val(rowData.one_barrel_n);
            $("#one_barrel_p").val(rowData.one_barrel_p);
            $("#one_barrel_k").val(rowData.one_barrel_k);
            $("#fertilizer_price").val(rowData.fertilizer_price);
            $("#fertilizer_price_type").val(rowData.fertilizer_type);
        }

        if(fertilizing_machine_type === "2"){
            //for 2 barrels
            var mainRowData = $("#jqGrid2").getRowData(1);
            if(mainRowData.fertilizer_type === '20') mainRowData.fertilizer_type =1;
            if(mainRowData.fertilizer_type === '500') mainRowData.fertilizer_type =2;

            $("#main_fertilizer_name").val(mainRowData.fertilizer_name);
            $("#main_fertilizer_n").val(mainRowData.fertilizer_n);
            $("#main_fertilizer_p").val(mainRowData.fertilizer_p);
            $("#main_fertilizer_k").val(mainRowData.fertilizer_k);
            $("#fertilizer_price").val(mainRowData.fertilizer_price);
            $("#fertilizer_price_type").val(mainRowData.fertilizer_type);

            var subRowData = $("#jqGrid2").getRowData(2);
            if(subRowData.fertilizer_type === '20') subRowData.fertilizer_type =1;
            if(subRowData.fertilizer_type === '500') subRowData.fertilizer_type =2;

            $("#sub_fertilizer_name").val(subRowData.fertilizer_name);
            $("#sub_fertilizer_n").val(subRowData.fertilizer_n);
            $("#sub_fertilizer_p").val(subRowData.fertilizer_p);
            $("#sub_fertilizer_k").val(subRowData.fertilizer_k);
            $("#fertilizer_price_sub").val(subRowData.fertilizer_price);
            $("#fertilizer_price_sub_type").val(subRowData.fertilizer_type);
        }
    };


})(loadGridTable ={});