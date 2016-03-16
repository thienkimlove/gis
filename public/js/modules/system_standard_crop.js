
$(document).ready(function () {
    $('#tabs').tabs();
    var lastSel;
    loadGrid();
    $(window).resize(
        resizeGrid()
    );
    setTimeout(function(){ loadGridData(); }, 200);
    $('select[name="crops_id"]').change(function(){
        loadGridData();
    });
    $('.btn-save-system-standard-crop').click(function(){
        $("#list1").jqGrid("editCell", 0, 0, false);
        $("#list2").jqGrid("editCell", 0, 0, false);
        $("#list3").jqGrid("editCell", 0, 0, false);
        var myGrid = $('#list2'),
            selRowId = myGrid.jqGrid ('getGridParam', 'selrow');
        myGrid .jqGrid('saveRow',selRowId);
        var myGrid3 = $('#list3'),
            selRowId = myGrid3.jqGrid ('getGridParam', 'selrow');
        myGrid3 .jqGrid('saveRow',selRowId);
        submitGrid();

    });
});
//function to show confirmation box when clear fertilization system for crop
function confirmClearCrop(){
    var cropName= $('select[name="crops_id"] option:selected').text();
    var confirmMsg = cropName+ Lang.get('common.fertilizer_system_clear_confirmation');
    showConfirm(confirmMsg,Lang.get('common.alert_title_message'),clearSystemFertilizationOfOneCrop);
}
function resizeGrid(){
    if (window.afterResize) {
        clearTimeout(window.afterResize);
    }
    window.afterResize = setTimeout(function() {
        var size=$("#tabs").parent().width()-13;
        jQuery("#list1").jqGrid('setGridWidth',size);
        jQuery("#list2").jqGrid('setGridWidth',size);
        jQuery("#list3").jqGrid('setGridWidth',size);
    }, 500);
}

function loadGridData(){
    var data;
    var fertilizerStandardId = $('#hidden-standard-id').val();
    standardCropId= $('select[name="crops_id"] option:selected').val();
    $.ajax({
        url: 'get-system-standard-crop-details/'+fertilizerStandardId +'/'+standardCropId,
        method:"get",
        success:function(resultData){
            data=resultData;
            data.nito[10].n+=Lang.get('common.standardcropdetail_nito_total');
            systemStandardCropN.refresh(data.nito);
            systemStandardCropP.refresh(data.photpho);
            systemStandardCropK.refresh(data.kali);
        }
    });
    refreshDelPk();
}
//function to clear system fertilization of one crop
//and reload form after clear
function clearSystemFertilizationOfOneCrop(){
    var data;
    var fertilizerStandardId = $('#hidden-standard-id').val();
    standardCropId= $('select[name="crops_id"] option:selected').val();
    $.ajax({
        url: 'clear-system-standard-crop-details/'+fertilizerStandardId +'/'+standardCropId,
        method:"get",
        success:function(resultData){
            if(resultData.code == 0){
                fancyMessage(Lang.get('common.fertilizer_system_cannot_clear_crop'), window.error_title);
                return false;
            }
            else{
                data=resultData;
                data.nito[10].n+=Lang.get('common.standardcropdetail_nito_total');
                systemStandardCropN.refresh(data.nito);
                systemStandardCropP.refresh(data.photpho);
                systemStandardCropK.refresh(data.kali);
            }

        }
    });
    refreshDelPk();
}
function loadGrid(){
    loadGridNito();
    loadGridPhotpho();
    loadGridKali();
}
function loadGridNito() {
    $("#list1").jqGrid({
        colModel: [
            {
                label: Lang.get('common.systemstandardcropdetail_nito_label1'),
                name: 'n',
                width: 80, align: 'right', sortable: false
            },
            {
                label: Lang.get('common.systemstandardcropdetail_nito_label2'),
                name: 'n_amount',
                width: 80, align: 'right', editable: true, sortable: false
            },
            {label: "1", name: 'division_amount1', width: 25, align: 'right', editable: true, sortable: false},
            {label: "2", name: 'division_amount2', width: 25, align: 'right', editable: true, sortable: false},
            {label: "3", name: 'division_amount3', width: 25, align: 'right', editable: true, sortable: false},
            {label: "4", name: 'division_amount4', width: 25, align: 'right', editable: true, sortable: false},
            {label: "5", name: 'division_amount5', width: 25, align: 'right', editable: true, sortable: false},
            {label: "6", name: 'division_amount6', width: 25, align: 'right', editable: true, sortable: false},
            {label: "7", name: 'division_amount7', width: 25, align: 'right', editable: true, sortable: false},
            {label: "8", name: 'division_amount8', width: 25, align: 'right', editable: true, sortable: false},
            {label: "9", name: 'division_amount9', width: 25, align: 'right', editable: true, sortable: false},
            {label: "10", name: 'division_amount10', width: 25, align: 'right', editable: true, sortable: false},
            {label: "11", name: 'division_amount11', width: 25, align: 'right', editable: true, sortable: false},
            {label: "12", name: 'division_amount12', width: 25, align: 'right', editable: true, sortable: false},
            {label: "13", name: 'division_amount13', width: 25, align: 'right', editable: true, sortable: false},
            {label: "14", name: 'division_amount14', width: 25, align: 'right', editable: true, sortable: false},
            {label: "15", name: 'division_amount15', width: 25, align: 'right', editable: true, sortable: false},
            {label: "16", name: 'division_amount16', width: 25, align: 'right', editable: true, sortable: false},
            {label: "17", name: 'division_amount17', width: 25, align: 'right', editable: true, sortable: false},
            {label: "18", name: 'division_amount18', width: 25, align: 'right', editable: true, sortable: false},
            {label: "19", name: 'division_amount19', width: 25, align: 'right', editable: true, sortable: false},
            {label: "20", name: 'division_amount20', width: 25, align: 'right', editable: true, sortable: false},
            {
                label: Lang.get('common.systemstandardcropdetail_nito_label4'),
                name: 'ratio',
                width: 30, align: 'right', editable: true, sortable: false
            },
            {
                name: 'new',
                hidden: true
            },
            {
                name: 'id',
                hidden: true
            }

        ],
        autowidth: true,
        scroll: 0,
        viewrecords: true, // show the current page, data rang and total records on the toolbar
        width:'auto',
        height: 'auto',
        loadonce: false,
        scrollOffset: 0,
        cellsubmit: "clientArray",
        cellEdit: isAdmin
    });
    $("#list1").jqGrid('setGroupHeaders', {
        useColSpanStyle: true,
        groupHeaders: [
            {
                startColumnName: 'division_amount1',
                numberOfColumns: 20,
                titleText: Lang.get('common.systemstandardcropdetail_nito_label3')
            }
        ]
    });
}
function  loadGridPhotpho() {
    $("#list2").jqGrid({
        colModel: [
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label2'),
                name: 'p',
                width: 180, align: 'right', editable: true, sortable: false, classes:"grid-int4"
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label3'),
                name: 'assessment',
                width: 180, align: 'left', editable: true, sortable: false
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label4'),
                name: 'fertilization_standard_amount',
                width: 180, align: 'right', editable: true, sortable: false, classes:"grid-int4"
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label5'),
                name: 'ratio',
                width: 182, align: 'right', editable: true, sortable: false,classes:"onlyDecimal4_2"
            },
            {
                name: 'id',
                hidden:true
            }

        ],
        scroll: true,
        width:'auto',
        height: 330,
        scrollOffset: true,
        pager: "#pager2",
        pginput: false,
        pgbuttons: false,
        viewrecords: true,
    });
    $("#list2").jqGrid('setGroupHeaders', {
        useColSpanStyle: true,
        groupHeaders: [
            {startColumnName:Lang.get('common.systemstandardcropdetail_photpho_label1'), numberOfColumns: 2, titleText: "P"}
        ]
    });
    if(isAdmin) {
        $("#list2")
            .navGrid('#pager2',{edit:false,add:false,del:false,search:false,refresh:false})
            .navButtonAdd('#pager2',{
                caption:"",
                buttonicon:"ui-icon-trash",
                onClickButton: function(){
                    var myGrid = $('#list2'),
                        selRowId = myGrid.jqGrid ('getGridParam', 'selrow');
                    jqgrid.storeId(selRowId, true, 'del-p');
                    $('#del-p-arr').val(JSON.stringify(jqgrid.getIds('del-p')));
                    myGrid.jqGrid('delRowData', selRowId);
                    myGrid.jqGrid('showAddEditButtons');
                    $('#list2').trigger( 'reloadGrid' );
                },
                position:"last"
            });
        $('#list2').inlineNav('#pager2',
            {
                add: true,
                edit: true,
                cancel: true
            });
    }
}
function loadGridKali() {
    $("#list3").jqGrid({
        colModel: [
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label2'),
                name: 'k',
                width: 180, align: 'right', editable: true, sortable: false, classes:"grid-int4"
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label3'),
                name: 'assessment',
                width: 180, align: 'left', editable: true, sortable: false
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label4'),
                name: 'fertilization_standard_amount',
                width: 180, align: 'right', editable: true, sortable: false, classes:"grid-int4"
            },
            {
                label:Lang.get('common.systemstandardcropdetail_photpho_label5'),
                name: 'ratio',
                width: 182, align: 'right', editable: true, sortable: false,classes:"onlyDecimal4_2"
            },
            {
                name: 'id',
                hidden: true
            }

        ],
        scroll: true,
        width:'auto',
        height: 330,
        scrollOffset: 0,
        pager: "#pager3",
        pginput: false,
        pgbuttons: false,
        viewrecords: true
    });
    $("#list3").jqGrid('setGroupHeaders', {
        useColSpanStyle: true,
        groupHeaders: [
            {startColumnName: Lang.get('common.systemstandardcropdetail_kali_label1'), numberOfColumns: 2, titleText: "K"}
        ]
    });
    if(isAdmin==true) {
        $("#list3")
            .navGrid('#pager3',{edit:false,add:false,del:false,search:false,refresh:false})
            .navButtonAdd('#pager3',{
                caption:"",
                buttonicon:"ui-icon-trash",
                onClickButton: function(){
                    var myGrid3 = $('#list3'),
                        selRowId = myGrid3.jqGrid ('getGridParam', 'selrow');
                    jqgrid.storeId(selRowId, true, 'del-k');
                    $('#del-k-arr').val(JSON.stringify(jqgrid.getIds('del-k')));
                    myGrid3.jqGrid('delRowData', selRowId);
                    myGrid3.jqGrid('showAddEditButtons');
                    $('#list3').trigger( 'reloadGrid' );
                },
                position:"last"
            });
        $('#list3').inlineNav('#pager3',
            {
                edit: true,
                add: true,
                cancel: true
            });
    }
}
function submitGrid(){
    var dataChangeN=$('#list1').jqGrid('getRowData');
    dataChangeN[10].n=dataChangeN[10].n.replace(Lang.get('common.standardcropdetail_nito_total'),"");
    $('#dataChangeN').val(JSON.stringify(dataChangeN));
    var dataChangeP=$('#list2').jqGrid('getRowData');
    $('#dataChangeP').val(JSON.stringify(dataChangeP));
    var dataChangeK=$('#list3').jqGrid('getRowData');
    $('#dataChangeK').val(JSON.stringify(dataChangeK));
    //validate data here
    if(nitrogenValidation(dataChangeN)){
        fancyMessage(Lang.get('common.systemstandardcropdetail_error_nito'), window.error_title);
        return false;
    }
    else if(photphoValidation(dataChangeP)){
        fancyMessage(Lang.get('common.systemstandardcropdetail_error_photpho'), window.error_title);
        return false;
    }
    else if(kaliValidation(dataChangeK)){
        fancyMessage(Lang.get('common.systemstandardcropdetail_error_kali'), window.error_title);
        return false;
    }
    //pass client validation
    //call script below
    var form = $('.system-standard-crop-details-frm');
    gisForm.clickSave(null, {
        formEle : form,
        callbackFunction : function(data){
            fancyMessage(data.message, window.info_title,
                function(){
                    if(data.code=== 200) {
                        loadGridData();
                    }
                });
        }
    });
}
function nitrogenValidation(dataChangeN){
    var result=false;
    var n=0;
    $.each(dataChangeN,function(i,val){
        if(val.n_amount==="") {
            n++;
        }
        if(val.ratio==="") result=true;
    });
    if(0<n && n<11) result=true;
    return result;
}
function photphoValidation(dataChangeP){
    var result=false;
    var n= 0,n3= 0,n4=0;
    $.each(dataChangeP,function(i,val){
        n++;
        if(val.p===""||val.assessment==="") result=true;
        if(val.fertilization_standard_amount!==""){
            n3++;
        }
        if(val.ratio!==""){
            n4++;
        }
    });
    if(n4<n || n>11||n==0){
        result=true;
    }
    return result;
}
function kaliValidation(dataChangeK){
    var result=false;
    var n= 0,n3= 0,n4=0;
    $.each(dataChangeK,function(i,val){
        n++;
        if(val.k===""||val.assessment==="") result=true;
        if(val.fertilization_standard_amount!==""){
            n3++;
        }
        if(val.ratio!==""){
            n4++;
        }
    });
    if(n4<n || n>11||n==0){
        result=true;
    }
    return result;
}
function refreshDelPk(){
    $('#del-p').val('');
    $('#del-k').val('');
    $('#del-p-arr').val('');
    $('#del-k-arr').val('');
};
(function(module){
    module.grid = {
        lastsel : '',
        iRow:-1,
        iCol:-1
    };
    module.cleanData = function(){
        $("#list1").jqGrid('clearGridData');
    };

    module.refresh = function (nito) {
        var grid = $("#list1");
        grid.jqGrid('setGridParam',{
            datatype: "local",
            data: nito
        }).trigger("reloadGrid");
    };

})(systemStandardCropN = {});
(function(module){
    module.grid = {
        lastsel : '',
        iRow:-1,
        iCol:-1
    };

    module.cleanData = function(){
        $("#list2").jqGrid('clearGridData');
    };

    module.refresh = function (photpho) {
        var grid = $("#list2");
        systemStandardCropP.cleanData();
        grid.jqGrid('setGridParam',{
            datatype: "local",
            data: photpho
        }).trigger("reloadGrid");
    };

})(systemStandardCropP = {});
(function(module){
    module.grid = {
        lastsel : '',
        iRow:-1,
        iCol:-1
    };
    module.cleanData = function(){
        $("#list3").jqGrid('clearGridData');
    };

    module.refresh = function (kali) {
        var grid = $("#list3");
        systemStandardCropK.cleanData();
        grid.jqGrid('setGridParam',{
            datatype: "local",
            data: kali
        }).trigger("reloadGrid");
    };

})(systemStandardCropK = {});
