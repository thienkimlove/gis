$(document).ready(function(){
    $('#map').height($(window).height()*73/100);
    $('#panel-tree').height($(window).height()*73/100);
    $( "#panel-left" ).resizable({grid: 50,handles: 'e',minHeight: 400,minWidth: Lang.get('common.tree_panel_min_width'),maxWidth: Lang.get('common.tree_panel_max_width')});
    $("#panel-left").bind("resize", function (event, ui) {
        $('#panel-right').width($(window).width() -(ui.size.width + 40));
        gisMap.map.updateSize()
    });
    //load tree when go to page.

    gisTree.loadTree( function(){
        $('#user_id_main').bind('change', function(){
            gisObject.user_id_main = $('#user_id_main').val();
            gisTree.loadTree();
        })

        $("#show").click(function(e){
            gisTree.loadTree();
            $('.data').on('loaded.jstree',function(e,data){
                $(this).jstree().open_all();
            })
        });
        $("#hide").click(function(){
            gisTree.loadTree(function(){
                var temp1=$('.data').jstree();
                $('.data').jstree().close_all();
                temp1.open_node(temp1.get_parent(temp1.get_selected()));
            });
        });
        var tree=$('.data');
        tree.jstree().clear_state();
        var last_active_layer_id= gisObject.config.state.last_active_layer_id;
        if(last_active_layer_id!=undefined && last_active_layer_id != 0) {
            var temp=$('.data').jstree();
            temp.deselect_all();
            temp.select_node(last_active_layer_id);
            var getNode=temp.get_node(temp.get_selected());
            if(getNode==false) return;
            if(getNode.type=='layer_terrain'){
                gisMap.defaultMap(getNode.id);
            }
            else{
                gisMap.loadMap(last_active_layer_id,getNode.type);
            }
            gisObject.layer_id = last_active_layer_id;
        }
        else if(!isAdmin && last_active_layer_id != 0){
            gisMap.loadMap(null,null,gisObject.session_user_id);
        }
        else {
            gisMap.defaultMap();
        }
    });

    //change color
    $('#change_color').click(function (evt) {
        evt.preventDefault();
        if (!gisObject.is_fertilizer) {
            bootbox.alert("This function is invalid");
            return false;
        }

        changingcolor.openChangingColor();
    });

    $('.user-map').click(function () {
        var user_id = (gisObject.user_id_main) ? gisObject.user_id_main : gisObject.config.current_user_id;
        if (user_id) {
            gisMap.loadMap(null, null, user_id);
        }
        return false;

    });


    //show form selection.

    $('.show-nito-map').click(function () {
        var user_id = (gisObject.user_id_main) ? gisObject.user_id_main : gisObject.config.current_user_id;
        $.fancybox([{
            href: window.base_url + '/map-prefix/nito/' + user_id,
            type: 'ajax',
            helpers: {
                overlay: {
                    closeClick: false
                }
            }
        }], {
            afterLoad: function (data) {
                try {
                    var json = $.parseJSON(data.content);
                    fancyMessage(json.message,window.error_title);
                    top.$.fancybox.close();
                    return false;
                } catch (err) {

                }
            }
        });
    });

    //those functions below in popup element so we need using $(document).on..

    function changeModeType(mode_type){
        //choose conditions
        if (mode_type ==  2) {
            gisObject.mode_selection_ids = [];
            var user_id = (gisObject.user_id_main) ? gisObject.user_id_main : gisObject.config.current_user_id;
            var map_id_arr = $('#layer-id').val().split("_");
            var crop_id = $('#crop-id').val();
            if(map_id_arr && crop_id){
                var fertility_map_id = map_id_arr[0];
                //load conditions from database if have.


                $.post(window.base_url + '/map-prefix/show-selection', {
                    user_id : user_id,
                    crop_id : crop_id,
                    fertility_map_id : fertility_map_id
                }, function(data){
                    if(data.length != undefined){
                        var response  = $.parseJSON(data);
                        gisObject.mode_selection_ids = [];
                        if (response) {
                            for (var key in response) {
                                gisObject.mode_selection_ids.push(key);
                            }
                        }
                    }else{
                        fancyAlert(Lang.get('common.map_prefix_nito'),Lang.get('common.info_title'));
                    }
                });
            }
        } else {
            gisObject.mode_selection_ids = [];
        }
        return true;
    }

    $(document).on('click', '.cancel-specification-map', function () {
        top.$.fancybox.close();
    });
    $(document).on('change', '#mode_type', function () {
        gisObject.mode_type = $(this).val();// 1 - all, 2 - condition.
        changeModeType(gisObject.mode_type);
    });
    $(document).on('change', '#crop-id', function () {
        var mode_type = $("#mode_type").val();
        if(mode_type){
            changeModeType(mode_type);
        }
    });
    $(document).on('change', '#layer-id', function () {
        var mode_type = $("#mode_type").val();
        if(mode_type){
            changeModeType(mode_type);
        }
    });

    $(document).on('click', '.btn-create-map', function (){

        var map_id_arr = $('#layer-id').val().split("_");
        var crop_id = $('#crop-id').val();
        var fertility_map_id = map_id_arr[0];
        var layer_id = map_id_arr[1];
        return $('.frm-validation-create-map').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {

                 $('.frm-validation-create-map').validationEngine('hideAll');
                if (status === false)
                    return false;
                else {
                    gisObject.fertility_map_id = fertility_map_id;
                    gisObject.crop_id = crop_id;
                    gisObject.layer_id = layer_id;
                    gisObject.is_fertilizer = false;

                    gisMap.startDraw = true;
                    var tempTree=$('.data').jstree();
                    tempTree.deselect_all();
                    tempTree.select_node(layer_id);
                    gisMap.loadMap(gisObject.layer_id);
                    $.ajax({
                        url: window.base_url + '/fertilizer/validate-specification',
                        data: {
                            crop_id: gisObject.crop_id,
                            fertility_id: gisObject.fertility_map_id,
                            layer_id: gisObject.layer_id
                        },
                        type: 'post',
                        success: function (data) {
                            var errorMessage = buildMessage(data.message);
                            if (data.code != 200) {
                                fancyAlertCallback(errorMessage, function () {
                                    if (data.code == 401) {
                                        window.location.href = window.base_url + 'login';
                                    } else if (data.code == 403) {
                                        window.location.href = window.permission_denined_url;
                                    }
                                });
                            }

                            top.$.fancybox.close();
                            if( gisObject.mode_type == 1){
                                bootbox.alert(Lang.get('common.fertilizer_variable_fertilization_map'));
                            }

                        }
                    });

                }
            }
        });
    });

});

