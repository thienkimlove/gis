/**
 fertilizer_table in resource/view/demo/map.blade.php
 */

(function(module){
    module.setEditing = function(){
        module.isEditing = true;
        module.isEdited = false;
    };

    module.setEdited = function(){
        if(module.isEdited) return;
        if(module.isEditing === false){
            loadGridTable.saveGrids();
            module.isEdited = true;
        }
        module.isEditing = false;
    };
    module.updateRow = function(rowId, n, p, k) {
        var gridId = "jqGrid3";
        $("#" + gridId).jqGrid("setCell", rowId, "n", n);
        $("#" + gridId).jqGrid("setCell", rowId, "p", p);
        $("#" + gridId).jqGrid("setCell", rowId, "k", k);
    };

    module.sumTable3 = function(){
        var gridId = "jqGrid3";
        var row1 = $("#"+gridId).getRowData(1);
        var row2 = $("#"+gridId).getRowData(2);
        var row3 = $("#"+gridId).getRowData(3);
        var row4 = $("#"+gridId).getRowData(4);

        if(row1.n==="") row1.n = 0;
        else $("#"+gridId).jqGrid("setCell", 1, "n", Number(row1.n).toFixed(1));
        if(row1.p==="") row1.p = 0;
        else $("#"+gridId).jqGrid("setCell", 1, "p", Number(row1.p).toFixed(1));
        if(row1.k==="") row1.k = 0;
        else $("#"+gridId).jqGrid("setCell", 1, "k", Number(row1.k).toFixed(1));

        if(row2.n==="") row2.n = 0;
        else $("#"+gridId).jqGrid("setCell", 2, "n", Number(row2.n).toFixed(1));
        if(row2.p==="") row2.p = 0;
        else $("#"+gridId).jqGrid("setCell", 2, "p", Number(row2.p).toFixed(1));
        if(row2.k==="") row2.k = 0;
        else $("#"+gridId).jqGrid("setCell", 2, "k", Number(row2.k).toFixed(1));

        if(row3.n==="") row3.n = 0;
        else $("#"+gridId).jqGrid("setCell", 3, "n", Number(row3.n).toFixed(1));
        if(row3.p==="") row3.p = 0;
        else $("#"+gridId).jqGrid("setCell", 3, "p", Number(row3.p).toFixed(1));
        if(row3.k==="") row3.k = 0;
        else $("#"+gridId).jqGrid("setCell", 3, "k", Number(row3.k).toFixed(1));

        if(row4.n==="") row4.n = 0;
        else $("#"+gridId).jqGrid("setCell", 4, "n", Number(row4.n).toFixed(1));
        if(row4.p==="") row4.p = 0;
        else $("#"+gridId).jqGrid("setCell", 4, "p", Number(row4.p).toFixed(1));
        if(row4.k==="") row4.k = 0;
        else $("#"+gridId).jqGrid("setCell", 4, "k", Number(row4.k).toFixed(1));

        var n = parseFloat(row1.n) + parseFloat(row2.n) + parseFloat(row3.n) + parseFloat(row4.n);
        var p = parseFloat(row1.p) + parseFloat(row2.p) + parseFloat(row3.p) + parseFloat(row4.p);
        var k = parseFloat(row1.k) + parseFloat(row2.k) + parseFloat(row3.k) + parseFloat(row4.k);

        $("#"+gridId).jqGrid("setCell", 5, "n", Number(n).toFixed(1));
        $("#"+gridId).jqGrid("setCell", 5, "p", Number(p).toFixed(1));
        $("#"+gridId).jqGrid("setCell", 5, "k", Number(k).toFixed(1));

    };

    module.sumTable4 = function(value, column){

        var grid = $('#jqGrid4');
        var row1 = grid.getRowData(1);
        var row2 = grid.getRowData(2);
        var row3 = grid.getRowData(3);
        //
        if(row1.n==="") row1.n = 0;
        else grid.jqGrid("setCell", 1, "n", Number(row1.n).toFixed(1));
        if(row1.p==="") row1.p = 0;
        else grid.jqGrid("setCell", 1, "p", Number(row1.p).toFixed(1));
        if(row1.k==="") row1.k = 0;
        else grid.jqGrid("setCell", 1, "k", Number(row1.k).toFixed(1));

        if(row2.n==="") row2.n = 0;
        else grid.jqGrid("setCell", 2, "n", Number(row2.n).toFixed(1));
        if(row2.p==="") row2.p = 0;
        else grid.jqGrid("setCell", 2, "p", Number(row2.p).toFixed(1));
        if(row2.k==="") row2.k = 0;
        else grid.jqGrid("setCell", 2, "k", Number(row2.k).toFixed(1));

        if(row3.n==="") row3.n = 0;
        else grid.jqGrid("setCell", 3, "n", Number(row3.n).toFixed(1));
        if(row3.p==="") row3.p = 0;
        else grid.jqGrid("setCell", 3, "p", Number(row3.p).toFixed(1));
        if(row3.k==="") row3.k = 0;
        else grid.jqGrid("setCell", 3, "k", Number(row3.k).toFixed(1));

        var n = grid.jqGrid('getCol', 'n', false, 'sum');
        var p = grid.jqGrid('getCol', 'p', false, 'sum');
        var k = grid.jqGrid('getCol', 'k', false, 'sum');
        grid.jqGrid('footerData','set', {fertilization_stage: Lang.get('common.creating_map_table_4_row4'), n: Number(n).toFixed(1), p: Number(p).toFixed(1), k: Number(k).toFixed(1)},'ui-row-ltr');
        $(".footrow-ltr").addClass("ui-row-ltr");
        $(".footrow-ltr").addClass("jqgrow");
    };

    module.submitCreatingMap = function(){

        var form = $('.creating-map-frm');
        gisForm.clickSave(null, {
            formEle : form,
            callbackFunction : function(data){

                reloadPage(data);
                fancyMessage(data.message, window.info_title,
                    function(){
                        if(data.code=== 200) {
                            top.$.fancybox.close();
                            $('#mapConfirmModal').modal('toggle');
                            if(data.data.isCreate==0){
                                window.location.href = window.base_url + '/reload';
                            }
                            else {
                                var new_layer = data.data.id;
                                gisTree.loadTree(function () {
                                    var tree2 = $('.data');
                                    tree2.jstree().clear_state();
                                    var temp = $('.data').jstree();
                                    temp.deselect_all();
                                    temp.select_node(new_layer);
                                    gisObject.layer_id = new_layer;
                                    gisMap.loadMap(new_layer, temp.get_node(temp.get_selected()).type);
                                });
                            }
                        }
                        else {
                            $('#mapConfirmModal').modal('toggle');
                            module.hideConfirm();

                        }
                    });
            }
        });
    };
    module.validateForm = function(){

        var form = $('.creating-map-frm');
        gisForm.validateForm({
            formEle : form
        });
    };

    module.refresh = function () {
        //jqgrid.clearIds('hidden-select');
        //jQuery("#jqGrid").jqGrid('setGridParam',{url: 'get-fertilizers', page: 1}).trigger("reloadGrid");
    };

    module.openCreatingMap = function () {
        //mapId = 1;

        var mapId = gisObject.fertility_map_id;
        var cropId = gisObject.crop_id;
        var mapInfoIds = gisObject.map_info_ids;
        var user_id_main = (gisObject.user_id_main)? gisObject.user_id_main : gisObject.config.current_user_id;

        //reset mode selection.

        gisObject.mode_type = null;
        gisObject.mode_selection_info_ids = [];


        if(cropId === undefined || cropId === "") cropId = 0;
        var url = window.base_url + '/creating-map/'+mapId+'/'+cropId;

        gisForm.openPopup(url,
            function(){
                //fertilizer.openStandardCrop(standardId);
            },
            {
                mapInfoIds: mapInfoIds,
                user_id_main:user_id_main
            }
        );
    };

    module.openMapViewer = function () {
        //mapId = 1;
        var mapId = gisObject.fertility_map_id;
        var cropId = gisObject.crop_id;
        var mapInfoIds = gisObject.map_info_ids;
        var user_id_main = gisObject.user_id_main;
        if(cropId === undefined || cropId === "") cropId = 0;
        var url = window.base_url + '/open-map-viewer/'+mapId+'/'+cropId;
        gisForm.openPopup(url,
            function(){
            },
            {
                mapInfoIds: mapInfoIds,
                user_id_main:user_id_main
            }
        );
    };
    var previousCrop = $('#crops_id').val();
    module.changeFertilizer = function(field){
        var current_user_id = gisObject.user_id_main ? gisObject.user_id_main : gisObject.session_user_id;
        var standardFertilizer = $('#fertilizer_standard_definition_id').val();
        if(standardFertilizer=='') standardFertilizer=0;
        var cropId = $('#crops_id').val();
        if(cropId === "") cropId = 0;
        var loading_url = window.base_url + "/get-list-standard-fertilizer/"+cropId+'/'+current_user_id+'/'+standardFertilizer;
        $.ajax({
            url:loading_url,
            //global: false, // Disable the ajaxStart trigger
            type : "GET",
            dateType:"json",
            success:function(response){
                var count = Object.keys(response.data).length;
                if(count==1){
                    $("#crops_id option[value='"+previousCrop+"']").prop('selected', true);
                    fancyMessage(Lang.get('common.changing_crop_cannot_find_any_fertilizer'),Lang.get('common.info_title'));
                    return false;
                }
                else{
                    module.processAfterChangingCrop(response);
                }
            }
        });
    };
    module.processAfterChangingCrop = function(response){
        reloadPage(response);
        var data = response.data;
        var initial = response.initial;
        var standardInitial = null;
        var selectedStandard = '';
        var select = $('#fertilizer_standard_definition_id');
        var fertilizerNow = select.val();
        var check = false;
        for(var key in data){
            if(key === '') continue;
            if(key==fertilizerNow) check=true;
        }
        if(check===false){
            bootbox.dialog({
                message: Lang.get('common.change_crop_when_create_fertilizer_map'),
                title: Lang.get('common.info_title'),
                buttons:{
                    danger:{
                        label: Lang.get('common.yes'),
                        className: 'btn-primary',
                        callback: function(){
                            select.empty();
                            select.append('<option value="">'+data['']+'</option>');

                            for(var key in initial){
                                if(key==='') continue;
                                if(initial[key]){
                                    standardInitial = key;
                                    break;
                                }
                            }

                            if(standardInitial == null)
                                standardInitial = response.defaultId;
                            for (var key in data) {
                                if(key==='') continue;
                                selectedStandard = key == standardInitial ? 'selected' : selectedStandard;
                                select.append('<option value='+key+' '+selectedStandard+'>'+data[key]+'</option>');

                                $('#fertilizer_notes').text('');
                                $('#fertilizer_range').text('');
                            }
                            previousCrop = $('#crops_id').val();
                            module.getFertilizers();
                        }
                    },
                    success:{
                        label: Lang.get('common.no'),
                        className: 'btn-primary',
                        callback: function(){
                            $("#crops_id option[value='"+previousCrop+"']").prop('selected', true);
                            previousCrop = $('#crops_id').val();
                        }
                    }
                }
            });
        } else{
            select.empty();
            select.append('<option value="">'+data['']+'</option>');

            for(var key in initial){
                if(key==='') continue;
                if(initial[key]){
                    standardInitial = key;
                    break;
                }
            }

            if(standardInitial == null)
                standardInitial = response.defaultId;
            for (var key in data) {
                if(key==='') continue;
                selectedStandard = key == standardInitial ? 'selected' : selectedStandard;
                select.append('<option value='+key+' '+selectedStandard+'>'+data[key]+'</option>');
            }
            select.val(fertilizerNow);
            previousCrop = $('#crops_id').val();
        }
    };

    module.changeOptions = function(p,k){

        var fertilizerId = $('#fertilizer_standard_definition_id').val();
        var cropId = $('#crops_id').val();
        if(fertilizerId === "" || cropId === "") return;
        var option_url = window.base_url + "/get-options/"+fertilizerId+"/"+cropId;
        $.ajax({
            url:option_url,
            type : "GET",
            dateType:"json",
            success:function(data){
                // photpho
                reloadPage(data);
                var p_select = $('#p');
                p_select.empty();
                p_select.append('<option value="">'+data.photpho[""]+'</option>');

                var selected = '';
                for (var key in data.photpho) {
                    if(key==='') continue;
                    selected = key==p ? 'selected' : '';
                    p_select.append('<option value='+key+' '+selected+'>'+data.photpho[key]+'</option>');
                }

                // kali
                var k_select = $('#k');
                k_select.empty();
                k_select.append('<option value="">'+data.kali['']+'</option>');

                for (var key in data.kali) {
                    if(key==='') continue;
                    selected = key==k ? 'selected' : '';
                    k_select.append('<option value='+key+' '+selected+'>'+data.kali[key]+'</option>');
                }

            }
        });

    };
    module._check1B= function(nito,photpho,kali){
        var fertilizer_n = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_n');
        if(fertilizer_n ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_n_required'),window.info_title);
            return 1;
        }
        if(nito){
            if(parseFloat(fertilizer_n) === 0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_n_required'),window.info_title);
                return 1;
            }
        }
        var fertilizer_p = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_p');
        if(fertilizer_p ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_p_required'),window.info_title);
            return 1;
        }
        if(photpho){
            if(parseFloat(fertilizer_p) === 0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_p_required'),window.info_title);
                return 1;
            }
        }
        var fertilizer_k = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_k');
        if(fertilizer_k ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_k_required'),window.info_title);
            return 1;
        }
        if(kali){
            if(parseFloat(fertilizer_k) === 0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_k_required'),window.info_title);
                return 1;
            }
        }
    };
    module._check2B= function(nito1,photpho1,kali1,nito2,photpho2,kali2){
        var fertilizer_n = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_n');
        if(fertilizer_n ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_n_required'),window.info_title);
            return 1;
        }
        if(nito1){
            if(parseFloat(fertilizer_n) === 0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_n_required'),window.info_title);
                return 1;
            }
        }
        var fertilizer_p = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_p');
        if(fertilizer_p ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_p_required'),window.info_title);
            return 1;
        }
        var fertilizer_k = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_k');
        if(fertilizer_k ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_k_required'),window.info_title);
            return 1;
        }
        var fertilizer_n_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_n');
        if(fertilizer_n_sub ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_n_sub_required'),window.info_title);
            return 1;
        }
        if(nito2){
            if(parseFloat(fertilizer_n_sub)===0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_n_sub_required'),window.info_title);
                return 1;
            }
        }
        var fertilizer_p_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_p');
        if(fertilizer_p_sub ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_p_sub_required'),window.info_title);
            return 1;
        }
        if(photpho2){
            if(parseFloat(fertilizer_p_sub)===0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_p_sub_required'),window.info_title);
                return 1;
            }
        }
        var fertilizer_k_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_k');
        if(fertilizer_k_sub ===""){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_k_sub_required'),window.info_title);
            return 1;
        }
        if(kali2){
            if(parseFloat(fertilizer_k_sub)===0){
                fancyMessage(Lang.get('common.creatingmap_fertilizer_k_sub_required'),window.info_title);
                return 1;
            }
        }
    };
    module.showConfirm = function(){
        loadGridTable.saveGrids();
        loadGridTable.saveClientData();

        var fertilizing_machine_type = $("input:radio[name='fertilizing_machine_type']:checked" ).val();
        switch(fertilizing_machine_type) {
            case "1":
                var fertilizer_name = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_fertilizer_name');
                if(fertilizer_name ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_name_required'),window.info_title);
                    return;
                }
                var fertilizer_type = $('#jqGrid1').jqGrid('getCell',1,'fertilizer_type');
                if(fertilizer_type ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_type_required'),window.info_title);
                    return;
                }
                var fertilizer_price = $('#jqGrid1').jqGrid('getCell',1,'fertilizer_price');
                if(fertilizer_price ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_price_required'),window.info_title);
                    return;
                }
                var control_methodology = $("#machine_type_1 input:radio[name='control_methodology']:checked" ).val();
                switch(control_methodology){
                    case "1":if(module._check1B(1,0,0)==1) return; break;
                    case "2":if(module._check1B(1,1,0)==1) return; break;
                    case "3":if(module._check1B(1,0,1)==1) return; break;
                    case "4":if(module._check1B(1,1,1)==1) return; break;
                    default:
                        fancyMessage(Lang.get('common.creatingmap_control_methodology_required'),window.info_title);
                        return;
                }
                var fertilizer_n = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_n');
                var fertilizer_p = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_p');
                var fertilizer_k = $('#jqGrid1').jqGrid('getCell',1,'one_barrel_k');
                var total_main = parseFloat(fertilizer_n)+parseFloat(fertilizer_k)+parseFloat(fertilizer_p);
                break;
            case "2":
                var fertilizer_name = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_name');
                if(fertilizer_name ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_name_required'),window.info_title);
                    return;
                }
                var fertilizer_type = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_type');
                if(fertilizer_type ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_type_required'),window.info_title);
                    return;
                }
                var fertilizer_price = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_price');
                if(fertilizer_price ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_price_required'),window.info_title);
                    return;
                }
                var fertilizer_name_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_name');
                if(fertilizer_name_sub ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_name_sub_required'),window.info_title);
                    return;
                }

                var fertilizer_type_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_type');
                if(fertilizer_type_sub ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_type_sub_required'),window.info_title);
                    return;
                }
                var fertilizer_price_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_price');
                if(fertilizer_price_sub ===""){
                    fancyMessage(Lang.get('common.creatingmap_fertilizer_price_sub_required'),window.info_title);
                    return;
                }

                var control_methodology = $("#machine_type_2 input:radio[name='control_methodology']:checked" ).val();
                switch(control_methodology) {
                    case "5":if(module._check2B(1,0,0,1,0,0)==1) return;
                        if($('#fixed_fertilizer_amount5').val() ===""){
                            fancyMessage(Lang.get('common.creatingmap_fixed_fertilizer_amount_required'),window.info_title);
                            return;
                        }
                        break;
                    case "6":
                        if(module._check2B(1,0,0,1,0,0)==1) return;
                        if($('#fixed_fertilizer_amount6').val() ===""){
                            fancyMessage(Lang.get('common.creatingmap_fixed_fertilizer_amount_required'),window.info_title);
                            return;
                        }
                        break;
                    case "7":if(module._check2B(1,0,0,0,1,0)==1) return; break;
                    case "8":if(module._check2B(1,0,0,0,0,1)==1) return; break;
                    default :
                        fancyMessage(Lang.get('common.creatingmap_control_methodology_required'),window.info_title);
                        return;
                }
                var fertilizer_n = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_n');
                var fertilizer_p = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_p');
                var fertilizer_k = $('#jqGrid2').jqGrid('getCell',1,'fertilizer_k');
                var fertilizer_n_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_n');
                var fertilizer_p_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_p');
                var fertilizer_k_sub = $('#jqGrid2').jqGrid('getCell',2,'fertilizer_k');
                var total_main = parseFloat(fertilizer_n)+parseFloat(fertilizer_k)+parseFloat(fertilizer_p);
                var total_sub = parseFloat(fertilizer_n_sub)+parseFloat(fertilizer_k_sub)+parseFloat(fertilizer_p_sub);
                break;
            default :
                fancyMessage(Lang.get('common.creatingmap_fertilizing_machine_type_required'),window.info_title);
                return;

        }

        if(total_main > 100){
            fancyMessage(Lang.get('common.creatingmap_fertilizing_total_main_ratio_maximum'),window.info_title);
            return;
        }

        if(typeof total_sub != 'undefined'){
            if(total_sub > 100){
                fancyMessage(Lang.get('common.creatingmap_fertilizing_total_sub_ratio_maximum'),window.info_title);
                return;
            }

        }
            var soil_analysis_type = $("input:radio[name='soil_analysis_type']:checked").val();
            switch (soil_analysis_type) {
                case "1":
                    break;
                case "2":
                    var p = $('#p').val();
                    if (p === "") {
                        fancyMessage(Lang.get('common.creatingmap_p_required'), window.info_title);
                        return;
                    }

                    var k = $('#k').val();
                    if (k === "") {
                        fancyMessage(Lang.get('common.creatingmap_k_required'), window.info_title);
                        return;
                    }
                    break;
                default :

                    fancyMessage(Lang.get('common.creatingmap_soil_analysis_type_required'), window.info_title);
                    return;
            }

        var isValid = gisForm.validateForm('creating-map-frm');
        if(!isValid) return;

        // Table4
        var data4 = jQuery('#jqGrid4').jqGrid('getRowData');

        var errorDatas =[];
        for (var i = data4.length-1; i >=0; i--) {
            var name = data4[i].fertilization_stage;
            var value = data4[i].n+data4[i].p+data4[i].k;
            if(value !== "" && name === ""){
                errorDatas.push(data4[i]);

            }
        }
        if(errorDatas.length > 0){
            fancyMessage(Lang.get('common.creatingmap_fertilizer_phase_required'),window.info_title);
            return;
        }

        var total = 0;
        if(fertilizing_machine_type === "1"){
            total = parseFloat("0"+$('#jqGrid1').jqGrid('getCell',1,'fertilizer_price'));
        }
        if(fertilizing_machine_type === "2"){
            total = parseFloat("0"+$('#jqGrid2').jqGrid('getCell',1,'fertilizer_price'));
            total += parseFloat("0"+$('#jqGrid2').jqGrid('getCell',2,'fertilizer_price'));
        }

        $('#soil_analysis_k_label').val($("#k option:selected").text());
        $('#soil_analysis_p_label').val($("#p option:selected").text());

        $.ajax({
            url:window.base_url+"/admin/get-map-confirm-viewer",
            method:"POST",
            data:$('.creating-map-frm').serialize(),
            cache: false,
            success:function(view){
                reloadPage(view);
                if(view.code==404){
                    fancyAlert(view.message,window.error_title)
                }
                else {
                    $("#mapConfirmModal").empty();
                    $("#mapConfirmModal").append(view);
                    $("#mapConfirmModal").modal({backdrop: 'static', keyboard: false});
                    $(".fancybox-overlay").css("display", "none");
                }
            }
        });


    };
    module.hideConfirm = function(){
        $(".fancybox-overlay").css("display", "block");
    };

    module.getFertilizers = function () {
        var cropId = $('#crops_id').val();
        if(cropId == ''){
            $('#fertilizer_standard_definition_id').val('');
            fancyMessage(Lang.get('common.create_fertilizer_map_crop_required'),Lang.get('common.info_title'));
            return false;
        }
        var fertilizerId = $('#fertilizer_standard_definition_id').val();
        var loading_url = window.base_url + "/get-fertilizer/"+fertilizerId+'/'+cropId;
        $.ajax({
            url:loading_url,
            //global: false, // Disable the ajaxStart trigger
            type : "GET",
            dateType:"json",
            success:function(data){
                reloadPage(data);
                if(data==0){
                    fancyMessage(Lang.get('common.crop_not_in_fertilizer_standard'),Lang.get('common.info_title'));
                    return false;
                }
                $('#fertilizer_notes').text(data.notes);
                $('#fertilizer_range').text(data.range_of_application);
                module.changeOptions();
            }
        });
    };

    module.changeMachineType = function(){
        var fertilizing_machine_type = $("input:radio[name='fertilizing_machine_type']:checked" ).val();

        if(fertilizing_machine_type === "1"){
            $("#machine_type_1").css("display", "block");
            $("#machine_type_2").css("display", "none");
            $("#new2").val(0);
            $("#new2").attr("disabled","disabled");
            $('#control_methodology_11').prop('checked',true)
            module.changeControlMethodology();
        }else if(fertilizing_machine_type === "2"){
            $("#machine_type_1").css("display", "none");
            $("#machine_type_2").css("display", "block");
            $("#new2").removeAttr("disabled");
            $('#control_methodology_21').prop('checked',true)
            module.changeControlMethodology();
        }
        $('.soil_analysis_radio1').prop("checked",true);
        $('.soil_analysis_value').hide();
    };

    module.changeAnalysisType = function(){
        var fertilizing_machine_type = $("input:radio[name='soil_analysis_type']:checked" ).val();
        if(fertilizing_machine_type === "1"){
            $("#analysis_type_2").css("display", "none");
        }else if(fertilizing_machine_type === "2"){
            $("#analysis_type_2").css("display", "block");
            $("#analysis_type_2_cover").css("display", "none");
        }
    };

    module.changeControlMethodology = function(){
        module.changeControlMethod();
        var fertilizing_machine_type = $("input:radio[name='fertilizing_machine_type']:checked" ).val();
        var control_methodology = $("#machine_type_2 input:radio[name='control_methodology']:checked" ).val();
        var method = fertilizing_machine_type + "_"+control_methodology;

        switch(method) {
            case "2_5":
                $('#fixed_fertilizer_amount6').attr('disabled', true);
                $('#fixed_fertilizer_amount5').attr('disabled', false);
                break;

            case "2_6":
                $('#fixed_fertilizer_amount6').attr('disabled', false);
                $('#fixed_fertilizer_amount5').attr('disabled', true);
                break;
            default:
                $('#fixed_fertilizer_amount5').attr('disabled', true);
                $('#fixed_fertilizer_amount6').attr('disabled', true);
                break;
        }
    };
    module.getData = function() {
        var select1 = $('#select1').val();

        var select3 = $('#select3').val();
        var crop = $("[name='crops_id']").val();


        if(select1<=2 && $('#select2').prop("disabled") == true){
            $("#select2").val("");
            $("#select2").prop('disabled', false);
        }
        var select2 = $('#select2').val();
        if(select1 > 2){
            select2 = null;
            $("#select2").val("3");
            $("#select2").prop('disabled', true);
        }

        if((select1 !== null && select2 !== null && select3 !== null && crop !== null) || (select1 > 2 && select3 !== null && crop !== null)){

            $.ajax({
                url: window.base_url + '/admin/organicmatter/get-data-byproduct',
                type: "post",
                data: {'select1': select1, 'select2': select2, 'select3': select3, 'crop': crop},
                success: function(data){
                    reloadPage(data);
                    if(data['message']){
                        $('#sub-byproduct-nito').val('');
                        $('#sub-byproduct-photpho').val('');
                        $('#sub-byproduct-kali').val('');
                        $('#standard-dry').val('');
                        $('#standard-rate').val('');
                    }
                    else {
                        $('#sub-byproduct-nito').val(data['n']);
                        $('#sub-byproduct-photpho').val(data['p']);
                        $('#sub-byproduct-kali').val(data['k']);
                        $('#standard-dry').val(data['standard_dry_weight']);
                        $('#standard-rate').val(data['standard_CN_ratio']);
                    }
                }
            });
        }
    };

    module.getDataGM = function() {
        var select1 = $('#selectgm1').val();
        if(select1==1){
            if($('#selectgm2').prop("disabled") == true || $('#selectgm2').val()==3){
                $("#selectgm2").prop('disabled', false);
                $('#selectgm2').val('');
            }

            $('.selectgm2 option[value="3"]').css('display', 'none');
            $('.selectgm2 option[value="2"]').css('display', '');
            $('.selectgm2 option[value="1"]').css('display', '');
        }else if(select1==2 || select1==4 || select1==5){
            $("#selectgm2").val('1');
            $("#selectgm2").prop('disabled', true);
        }else if(select1==3){
            if($('#selectgm2').prop("disabled") == true || $('#selectgm2').val()==1){
                $("#selectgm2").prop('disabled', false);
                $('#selectgm2').val('');
            }

            $('.selectgm2 option[value="1"]').css('display', 'none');
            $('.selectgm2 option[value="2"]').css('display', '');
            $('.selectgm2 option[value="3"]').css('display', '');
        }else if(select1==6 || select1==7){
            $("#selectgm2").val('2');
            $("#selectgm2").prop('disabled', true);
        }
        var select2 = $('#selectgm2').val();
        var select3 = $('#selectgm3').val();
        var kali = $('#kali-rate').val() * 0.8;
        kali = kali.toFixed(1);
        var crop = $("[name='crops_id']").val();;

        if (select3 == 2 && (crop == 7||crop == 5||crop == 6)) {
            $("#kali-rate").prop('disabled', false);
        }else {
            $('#kali-rate').val('');
            $("#kali-rate").prop('disabled', true);
        }

        if(select1 !== null && select2 !== null && select3 !== null && crop !== null){

            $.ajax({
                url: window.base_url + '/admin/organicmatter/get-data-greenmanure',
                type: "post",
                data: {'select1': select1, 'select2': select2, 'select3': select3, 'crop': crop},
                success: function(data){
                    reloadPage(data);
                    if(data['message']){
                        $('#sub-greenmanure-nito').val('');
                        $('#sub-greenmanure-photpho').val('');
                        $('#sub-greenmanure-kali').val('');
                        $('#gm-standard-dry').val('');
                        $('#gm-standard-rate').val('');
                    }
                    else {
                        $('#sub-greenmanure-nito').val(data['n']);
                        $('#sub-greenmanure-photpho').val(data['p']);
                        if(kali > 0)
                            $('#sub-greenmanure-kali').val(kali);
                        else $('#sub-greenmanure-kali').val(data['k']);
                        $('#gm-standard-dry').val(data['standard_dry_weight']);
                        $('#gm-standard-rate').val(data['standard_CN_ratio']);
                    }
                }
            });
        }
        return;
    };

    module.getDataCP = function() {
        var select1 = $('#selectcp1').val();
        var select3 = $('#selectcp3').val();
        var dry_matter = $('#dry-matter').val();
        var select2 = $('#selectcp2').val();
        var crop = $("[name='crops_id']").val();

        if (select1 !== null && select3 == 1 && dry_matter !== null) {
            $.ajax({
                url: window.base_url + '/admin/organicmatter/get-data-compost',
                type: "post",
                data: {'select1': select1},
                success: function (data) {
                    reloadPage(data);
                    var n = data['n']*(dry_matter / data['dry_matter_content']);
                    var p = data['p']*(dry_matter / data['dry_matter_content']);
                    var k = data['k']*(dry_matter / data['dry_matter_content']);
                    n = n.toFixed(1);
                    p = p.toFixed(1);
                    k = k.toFixed(1);

                    $('#seibun-nito').val(n);
                    $('#seibun-photpho').val(p);
                    $('#seibun-kali').val(k);
                    module.npkRecommend();
                }
            });
        }

        return;
    };

    module.getDataCP2 = function() {
        if($('#selectcp3').val() === '1'){
            $("#dry-matter").prop('disabled', false);
            $("#seibun-nito").prop('disabled', true);
            $("#seibun-photpho").prop('disabled', true);
            $("#seibun-kali").prop('disabled', true);
        }else {
            $("#dry-matter").prop('disabled', true);
            $("#seibun-nito").prop('disabled', false);
            $("#seibun-photpho").prop('disabled', false);
            $("#seibun-kali").prop('disabled', false);
        }
    }

    module.getDataCP3 = function(){
        var select1 = $('#selectcp1').val();
        var select2 = $('#selectcp2').val();
        var crop = $("[name='crops_id']").val();
        if(select1!=1){
            $("#selectcp2").val('3');
            $("#selectcp2").prop('disabled', true);
            select2=3;
        }else if(select1==1 && $('#selectcp2').prop("disabled") == true){
            $("#selectcp2").val('');
            select2=null;
            $("#selectcp2").prop('disabled', false);
        }
        if(select1 !== null && select2 !== null && crop !== null){
            $.ajax({
                url: window.base_url + '/admin/organicmatter/get-data-fertilizer-efficiency',
                type: "post",
                data: {'select1': select1, 'select2': select2, 'crop': crop},
                success: function (data) {
                    reloadPage(data);
                    $('#sub-compost-nito').val(data['n']);
                    $('#sub-compost-photpho').val(data['p']);
                    $('#sub-compost-kali').val(data['k']);
                    module.npkRecommend();
                }
            });
        }

    };

    module.npkRecommend = function() {
        var scn = $('#sub-compost-nito').val();
        var scp = $('#sub-compost-photpho').val();
        var sck = $('#sub-compost-kali').val();
        var sn = $('#seibun-nito').val();
        var sp = $('#seibun-photpho').val();
        var sk = $('#seibun-kali').val();
        var ci = $('#compost-input').val();
        if (scn !== '' && scp !== '' && sck !== '' && sn !== '' && sp !== '' && sk !== '' && ci !== ''){
            var n_dp = (sn * scn * ci) / 100;
            var n = n_dp.toFixed(1);
            var p_dp = (sp *scp *ci) / 100;
            var p = p_dp.toFixed(1);
            var k_dp = (sk * sck *ci) / 100;
            var k = k_dp.toFixed(1);

            $('#compost-nito').val(n);
            $('#compost-photpho').val(p);
            $('#compost-kali').val(k);
        }
    };

    module.changeControlMethod = function(){
        var soils_analysis_type = $("input:radio[name='control_methodology']:checked" ).val();
        if(soils_analysis_type === "1"){
            $("input:radio[id='optionsRadios1 soil_analysis_radio1']").prop("checked",true);
            $(".soil_analysis_value").css("display", "none");
        }else $(".soil_analysis_value").css("display", "block");
    }
})(creatingmap = {});
function format2(n, currency) {
    var result = currency + "" + n.toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    result = result.substring(0, result.length-2);
    return result;
}
$(document).ready(function(){
    //$("input:radio[name='fertilizing_machine_type']").on('change',function(){
    //    $('#optionsRadios1').prop("checked",true);
    //    $('.soil_analysis_value').hide();
    //    //
    //    //if($("input:radio[name='fertilizing_machine_type']:checked" ).val()=== "1"){
    //    //    $("#jqGrid1").jqGrid('setGridWidth',jQuery("#machine_type_1").width() - 20);
    //    //}else if($("input:radio[name='fertilizing_machine_type']:checked" ).val() === "2"){
    //    //    $("#jqGrid2").jqGrid('setGridWidth',jQuery("#machine_type_2").width() - 20);
    //    //}
    //});
})
$(function(){
    $(document).on('change','#k',function(){
        $('#soil_analysis_k_label').val($("#k option:selected").text());
    })
    $(document).on('change','#p',function(){
        $('#soil_analysis_p_label').val($("#p option:selected").text());
    })
})


