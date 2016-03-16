(function($){
    $.fn.outside = function(ename, cb){
        return this.each(function(){
            var $this = $(this),
                self = this;
            $(document).bind(ename, function tempo(e){
                if(e.target !== self && !$.contains(self, e.target)){
                    cb.apply(self, [e]);
                    if(!self.parentNode) $(document.body).unbind(ename, tempo);
                }
            });
        });
    };
}(jQuery));

(function(module){

    module.show_all = true;

    module._getType = function() {
        if (gisObject.config.is_admin == 1) {
            return {
                "#" : {
                    "max_depth" : 2,
                    "valid_children" : [ "folder_admin","folder_fertility","folder_fertilizer","folder_terrain","folder_bin"]
                },
                "folder_admin" : {
                    "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_fertility" : {
                    "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_fertilizer" : {
                    "valid_children" : [ "layer_fertilizer","layer_fertilizer_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_terrain" : {
                    "valid_children" : [ "layer_terrain","layer_terrain_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_bin" : {
                    "valid_children" : ["layer_fertility","layer_fertilizer","layer_fertility_hidden","layer_fertilizer_hidden","layer_admin","layer_admin_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "layer_admin" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_admin_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                },
                "layer_fertility" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },"layer_fertility_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                },
                "layer_fertilizer" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_fertilizer_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                },
                "layer_terrain" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_terrain_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                }

            };
        } else {
            return {
                "#" : {
                    "max_depth" : 2,
                    "valid_children" : [ "folder_admin","folder_fertility","folder_fertilizer","folder_terrain","folder_bin"]
                },
                "folder_admin" : {
                    "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_fertility" : {
                    "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_fertilizer" : {
                    "valid_children" : [ "layer_fertilizer","layer_fertilizer_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_terrain" : {
                    "valid_children" : [ "layer_terrain","layer_terrain_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "folder_bin" : {
                    "valid_children" : ["layer_fertility","layer_fertilizer","layer_fertility_hidden","layer_fertilizer_hidden"],
                    "max_depth" : 1,
                    "is_draggble" : false
                },
                "layer_admin" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_fertility" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },"layer_fertility_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                },
                "layer_fertilizer" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_fertilizer_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                },
                "layer_terrain" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "jstree-file"
                },
                "layer_terrain_hidden" : {
                    "valid_children" : "none",
                    "max_children" : 0,
                    "max_depth" : 0,
                    "icon" : "glyphicon glyphicon-file"
                }

            };
        }
    };
    module._getContextMenu = function(){
        if (gisObject.config.is_admin == 1) {
            return {
                "items": function ($node) {
                    var tmp = $.jstree.defaults.contextmenu.items();
                    tmp.remove.label=Lang.get('common.folder_button_delete');
                    tmp.rename.label=Lang.get('common.folder_button_rename');
                    delete tmp.ccp.submenu;
                    delete tmp.ccp.action;
                    if(this.get_parent($node) === "#") {
                        delete tmp.layer_restore;
                        delete tmp.fertilizer_main_function;
                        delete tmp.fertilizer_2;
                        delete tmp.fertilizer_pdf;
                        delete tmp.fertilizer_buy;
                        delete tmp.create;
                        delete tmp.ccp;
                    }
                    else if(this.get_type($node.parent)=="folder_bin"){
                        delete tmp.fertilizer_main_function;
                        delete tmp.fertilizer_2;
                        delete tmp.fertilizer_pdf;
                        delete tmp.fertilizer_buy;
                        delete tmp.create;
                        delete tmp.ccp;
                        delete tmp.rename;
                        tmp.layer_restore.label= Lang.get('common.mouse_right_click_layer_restore');
                        tmp.layer_restore.action =function() {
                            $.ajax({
                                url: window.base_url + '/folders/layer-restore/'+$node.id,
                                type: "GET",
                                success: function (data) {
                                    if (data.code != 200) {
                                        fancyMessage(data.message, Lang.get('common.error_title'), function () {
                                            top.location.reload();
                                            return true;
                                        });
                                    }
                                    else if(data.code==200){
                                        gisTree.loadTree();
                                        gisMap.loadMap($node.id,$node.type);
                                    }
                                }
                            });
                        }
                    }
                    else{
                        var hidden=false;
                        if($node.type.indexOf('hidden')>1){
                            tmp.ccp.label= Lang.get('common.mouse_right_click_visible');
                            hidden=false
                        }
                        else {
                            tmp.ccp.label = Lang.get('common.mouse_right_click_invisible');
                            hidden=true;
                        }
                        if($node.type=='layer_terrain'){
                            delete tmp.layer_restore;
                            delete tmp.fertilizer_main_function;
                            delete tmp.fertilizer_2;
                            delete tmp.fertilizer_pdf;
                            delete tmp.fertilizer_buy;
                            delete tmp.create;
                            delete tmp.ccp;
                            delete tmp.remove;
                        }
                        else{
                            if($node.type=='layer_fertilizer'||$node.type=='layer_fertilizer_hidden'){
                                delete tmp.layer_restore;
                                tmp.fertilizer_main_function.label = Lang.get('common.mouse_right_click_main_funtion');
                                tmp.fertilizer_main_function.submenu = {
                                    "mouse_right_click_edit_fertilizer" : {
                                        "separator_before"	: false,
                                        "separator_after"	: false,
                                        "label"				: Lang.get('common.mouse_right_click_edit_fertilizer'),
                                        "action"			: function (data) {
                                            $.fancybox([ {
                                                href : window.base_url + '/fertilizer-edit/'+$node.li_attr.fertilizer_map_id,
                                                type : 'ajax'
                                            } ], {
                                                helpers: {
                                                    overlay: { closeClick: false } //Disable click outside event
                                                },
                                                afterLoad : function(data) {
                                                    try {
                                                        reloadPage(data);
                                                        var json = $.parseJSON(data.content);
                                                        if (json.code == 404) {
                                                            top.$.fancybox.close();
                                                            fancyMessage(json.message, Lang.get('common.error_title'), function () {
                                                                window.location.reload();
                                                            });
                                                        }
                                                        else {
                                                            fancyMessage(json.message, Lang.get('common.error_title'), function () {
                                                                top.$.fancybox.close();
                                                                reloadPage(data);
                                                            });
                                                        }
                                                    }catch (err) {
                                                    }
                                                },
                                                afterShow: function(){
                                                    window.setTimeout(function() {
                                                        resizeGridEdit('jqGrid1', 'jqGrid2', 'jqGrid3', 'jqGrid4');
                                                    },1000);
                                                }
                                            });
                                        }
                                    },
                                    "mouse_right_click_merge_fertilizer" : {
                                        "separator_before"	: false,
                                        "separator_after"	: false,
                                        "label"				: Lang.get('common.mouse_right_click_merge_fertilizer'),
                                        "action"			: function (data) {

                                            bootbox.dialog({
                                                message: Lang.get('common.mouse_right_click_merge_fertilizer_confirm'),
                                                title: Lang.get('common.info_title'),
                                                buttons: {
                                                    success: {
                                                        label: Lang.get('common.yes_button'),
                                                        className: "btn-primary",
                                                        callback: function () {
                                                            gisObject.layer_id = $node.id;
                                                            gisMap.startDraw = true;
                                                            gisMap.helpMsg = 'Click to start drawing';
                                                            gisMap.loadMap($node.id, $node.type);
                                                        }
                                                    }
                                                }
                                            });

                                        }
                                    },
                                    "mouse_right_click_change_color_fertilizer" : {
                                        "separator_before"	: false,
                                        "separator_after"	: false,
                                        "label"				: Lang.get('common.mouse_right_click_change_color_fertilizer'),
                                        "action"			: function (data) {
                                            gisObject.layer_id = $node.id;
                                            gisMap.startDraw = false;
                                            gisMap.loadMap($node.id,$node.type);
                                            changingcolor.openChangingColor();

                                        }
                                    }
                                };


                                tmp.create.label= Lang.get('common.mouse_right_click_view_properties');
                                tmp.create.action=function(){
                                    $.fancybox([ {
                                        href : window.base_url + '/folders/get-fertilizer-properties/'+$node.id,
                                        type : 'ajax'
                                    } ],{
                                        helpers: {
                                        overlay: { closeClick: false } //Disable click outside event
                                        },
                                        afterLoad : function (data) {
                                            try {
                                                var json = $.parseJSON(data.content);
                                                top.$.fancybox.close();
                                                reloadPage(data);
                                                return false;
                                            } catch (err) {

                                            }
                                        }
                                    });
                                };


                                tmp.fertilizer_2.label= Lang.get('common.option_fertilizer_out_predict');
                                tmp.fertilizer_2.action=function(){
                                    gisMap.loadExportMap($node.id, true);
                                    gisObject.layer_id = $node.id;
                                    gisMap._updateState();
                                };

                                tmp.fertilizer_pdf.label= Lang.get('common.mouse_right_click_export_pdf');
                                tmp.fertilizer_pdf.action=function(){
                                    gisMap.loadExportMap($node.id);
                                    gisObject.layer_id = $node.id;
                                    gisMap._updateState();
                                };
                                tmp.fertilizer_buy.label= Lang.get('common.mouse_right_click_buy_fertilizer_map');
                                tmp.fertilizer_buy.action=function(){
                                    //display confirmation box
                                    var confirmMsg = Lang.get('common.fertilizer_map_download_confirmation');
                                        $.ajax({
                                            url:window.base_url + '/folders/download-fertilizer-map/'+$node.id,
                                            global: false, // Disable the ajaxStart trigger
                                            type : "GET",
                                            success : function(data) {
                                                if(data.code==404){
                                                    fancyMessage(data.message,Lang.get('common.error_title'),function(){
                                                        top.location.reload();
                                                        return true;
                                                    });
                                                }
                                                else if (data.canShowPopup){
                                                    gisForm.openPopup(window.base_url + '/folders/buy-fertilizer-view/' + $node.id + '/' + data.unpaidMesh);
                                                    reloadPage(data);
                                                }
                                                else {
                                                    window.location = window.base_url + '/download-file-csv/' + $node.id;
                                                    $.fancybox.close();
                                                }
                                            }
                                        });
                                };
                            }
                            else {
                                delete tmp.layer_restore;
                                delete tmp.fertilizer_main_function;
                                delete tmp.create;
                                delete tmp.fertilizer_2;
                                tmp.fertilizer_pdf.label= Lang.get('common.mouse_right_click_export_pdf');
                                tmp.fertilizer_pdf.action=function(){
                                    gisMap.loadExportMap($node.id);
                                    gisObject.layer_id = $node.id;
                                    gisMap._updateState();
                                };
                                delete tmp.fertilizer_buy;
                            }
                            tmp.ccp.action = function(){
                                $.ajax({
                                    url: window.base_url + '/folders/update-layer/'+$node.id,
                                    data: {
                                        folderId: $node.id,
                                        is_invisible_layer : hidden,
                                        name: $node.text
                                    },
                                    type:  'post',
                                    success: function( data ){
                                        reloadPage(data);
                                        if(data.code == 401) {
                                            window.location.href = window.base_url + '/login';
                                        }
                                        if(data.code == 403){
                                            window.location.href = window.permission_denined_url;
                                        }
                                        module.show_all = true;
                                        module.loadTree();
                                        if(hidden==true)
                                        gisMap.defaultMap($node.id);
                                        else if($node.type.indexOf('hidden')==17)
                                        gisMap.loadMap($node.id,'layer_fertilizer');
                                        else  gisMap.loadMap($node.id,$node.type);
                                    }
                                })
                            }
                        }

                    }
                    return tmp;
                }
            };
        } else {
            return {
                "items": function ($node) {
                    var tmp = $.jstree.defaults.contextmenu.items();

                    tmp.remove.label=Lang.get('common.folder_button_delete');
                    tmp.rename.label=Lang.get('common.folder_button_rename');
                    delete tmp.ccp.submenu;
                    delete tmp.ccp.action;

                    if(this.get_parent($node) === "#") {
                        delete tmp.layer_restore;
                        delete tmp.fertilizer_main_function;
                        delete tmp.fertilizer_2;
                        delete tmp.fertilizer_pdf;
                        delete tmp.fertilizer_buy;
                        delete tmp.create;
                        delete tmp.ccp;
                        delete tmp.remove;
                        delete tmp.rename;
                    }
                    else if(this.get_type($node.parent)=="folder_bin"){
                        delete tmp.fertilizer_main_function;
                        delete tmp.fertilizer_2;
                        delete tmp.fertilizer_pdf;
                        delete tmp.fertilizer_buy;
                        delete tmp.create;
                        delete tmp.ccp;
                        delete tmp.rename;
                        tmp.layer_restore.label= Lang.get('common.mouse_right_click_layer_restore');
                        tmp.layer_restore.action =function() {
                            $.ajax({
                                url: window.base_url + '/folders/layer-restore/'+$node.id,
                                type: "GET",
                                success: function (data) {
                                    if (data.code != 200) {
                                        fancyMessage(data.message, Lang.get('common.error_title'), function () {
                                            top.location.reload();
                                            return true;
                                        });
                                    }
                                    else if(data.code==200){
                                        gisTree.loadTree();
                                    }
                                }
                            });
                        }
                    }
                    else{
                        var hidden=false;
                        if($node.type.indexOf('hidden')>1){
                            tmp.ccp.label= Lang.get('common.mouse_right_click_visible');
                            hidden=false
                        }
                        else {
                            tmp.ccp.label = Lang.get('common.mouse_right_click_invisible');
                            hidden=true;
                        }
                        if($node.type=='layer_fertility'||$node.type=='layer_fertility_hidden'){
                            delete tmp.layer_restore;
                            delete tmp.fertilizer_main_function;
                            delete tmp.fertilizer_2;
                            tmp.fertilizer_pdf.label= Lang.get('common.mouse_right_click_export_pdf');
                            tmp.fertilizer_pdf.action=function(){
                                gisMap.loadExportMap($node.id);
                                gisObject.layer_id = $node.id;
                                gisMap._updateState();
                            };
                            delete tmp.fertilizer_buy;
                            delete tmp.create;
                            delete tmp.remove;
                            if(isGuest==true){
                                delete tmp.rename;
                            }
                        }
                        else if($node.type=='layer_fertilizer'||$node.type=='layer_fertilizer_hidden'){
                            delete tmp.layer_restore;
                            tmp.fertilizer_main_function.label = Lang.get('common.mouse_right_click_main_funtion');
                            tmp.fertilizer_main_function.submenu = {
                                "mouse_right_click_edit_fertilizer" : {
                                    "separator_before"	: false,
                                    "separator_after"	: false,
                                    "label"				: Lang.get('common.mouse_right_click_edit_fertilizer'),
                                    "action"			: function (data) {
                                        $.fancybox([ {
                                            href : window.base_url + '/fertilizer-edit/'+$node.li_attr.fertilizer_map_id,
                                            type : 'ajax'
                                        } ], {
                                            helpers: {
                                                overlay: { closeClick: false } //Disable click outside event
                                            },
                                            afterLoad : function(data) {
                                                try {
                                                    var json = $.parseJSON(data.content);
                                                    if (json.code == 404) {
                                                        top.$.fancybox.close();
                                                        fancyMessage(json.message, Lang.get('common.error_title'), function () {
                                                            window.location.reload();
                                                        });
                                                    }
                                                    else {
                                                        fancyMessage(json.message, Lang.get('common.error_title'), function () {
                                                            top.$.fancybox.close();
                                                            reloadPage(data);
                                                        });
                                                    }
                                                }catch (err) {
                                                }
                                            },
                                            afterShow: function(){
                                                window.setTimeout(function() {
                                                    resizeGridEdit('jqGrid1', 'jqGrid2', 'jqGrid3', 'jqGrid4');
                                                },1000);
                                            }
                                        });
                                    }
                                },
                                "mouse_right_click_merge_fertilizer" : {
                                    "separator_before"	: false,
                                    "separator_after"	: false,
                                    "label"				: Lang.get('common.mouse_right_click_merge_fertilizer'),
                                    "action"			: function (data) {
                                        bootbox.dialog({
                                            message: Lang.get('common.mouse_right_click_merge_fertilizer_confirm'),
                                            title: Lang.get('common.info_title'),
                                            buttons: {
                                                success: {
                                                    label: Lang.get('common.yes_button'),
                                                    className: "btn-primary",
                                                    callback: function () {
                                                        gisObject.layer_id = $node.id;
                                                        gisMap.startDraw = true;
                                                        gisMap.helpMsg = 'Click to start drawing';
                                                        gisMap.loadMap($node.id, $node.type);
                                                    }
                                                }
                                            }
                                        });
                                    }
                                },
                                "mouse_right_click_change_color_fertilizer" : {
                                    "separator_before"	: false,
                                    "separator_after"	: false,
                                    "label"				: Lang.get('common.mouse_right_click_change_color_fertilizer'),
                                    "action"			: function (data) {
                                        gisObject.layer_id = $node.id;
                                        gisMap.startDraw = false;
                                        gisMap.loadMap($node.id,$node.type);
                                        changingcolor.openChangingColor();

                                    }
                                }
                            };


                            tmp.create.label= Lang.get('common.mouse_right_click_view_properties');
                            tmp.fertilizer_2.label= Lang.get('common.option_fertilizer_out_predict');
                            tmp.fertilizer_2.action=function(){
                                gisMap.loadExportMap($node.id, true);
                                gisObject.layer_id = $node.id;
                                gisMap._updateState();
                            };
                            tmp.fertilizer_pdf.label= Lang.get('common.mouse_right_click_export_pdf');
                            tmp.fertilizer_pdf.action=function(){
                                gisMap.loadExportMap($node.id);
                                gisObject.layer_id = $node.id;
                                gisMap._updateState();
                            };

                            tmp.fertilizer_buy.label= Lang.get('common.mouse_right_click_buy_fertilizer_map');
                            tmp.create.action=function(){
                                $.fancybox([ {
                                    href : window.base_url + '/folders/get-fertilizer-properties/'+$node.id,
                                    type : 'ajax'
                                } ], {
                                    helpers: {
                                        overlay: { closeClick: false } //Disable click outside event
                                    },
                                    afterLoad : function(data) {
                                        try {
                                            var json = $.parseJSON(data.content);
                                            top.$.fancybox.close();
                                            reloadPage(data);
                                            return false;
                                        } catch (err) {

                                        }
                                    }
                                });
                            };
                            tmp.fertilizer_buy.action=function(){
                                //display confirmation box
                                var confirmMsg = Lang.get('common.fertilizer_map_download_confirmation');
                                    $.ajax({
                                        url:window.base_url + '/folders/download-fertilizer-map/'+$node.id,
                                        global: false, // Disable the ajaxStart trigger
                                        type : "GET",
                                        success : function(data) {
                                            if(data.code==404){
                                                fancyMessage(data.message,Lang.get('common.error_title'),function(){
                                                    top.location.reload();
                                                    return true;
                                                });
                                            }
                                            else if (data.canShowPopup) {
                                                gisForm.openPopup(window.base_url + '/folders/buy-fertilizer-view/' + $node.id + '/' + data.unpaidMesh);
                                                reloadPage(data);
                                            }
                                            else {
                                                window.location = window.base_url + '/download-file-csv/' + $node.id;
                                                $.fancybox.close();
                                            }
                                        }
                                    });
                            };
                            if(isGuest==true){
                                delete tmp.fertilizer_buy;
                                delete tmp.remove;
                            }
                        }
                        if($node.type=='layer_terrain'){
                            delete tmp.layer_restore;
                            delete tmp.fertilizer_main_function;
                            delete tmp.ccp;
                            delete tmp.remove;
                            delete tmp.rename;
                            delete tmp.fertilizer_2;
                            delete tmp.fertilizer_pdf;
                            delete tmp.fertilizer_buy;
                            delete tmp.create;
                        }
                        else{
                            tmp.ccp.action = function () {
                                $.ajax({
                                    url: window.base_url + '/folders/update-layer/' + $node.id,
                                    data: {
                                        folderId: $node.id,
                                        is_invisible_layer: hidden,
                                        name: $node.text
                                    },
                                    type: 'post',
                                    success: function (data) {
                                        reloadPage(data);
                                        if (data.code == 401) {
                                            window.location.href = window.base_url + '/login';
                                        }
                                        if (data.code == 403) {
                                            window.location.href = window.permission_denined_url;
                                        }
                                        module.show_all = true;
                                        module.loadTree();
                                        if (hidden == true)
                                            gisMap.defaultMap($node.id);
                                        else if ($node.type.indexOf('hidden') == 17)
                                            gisMap.loadMap($node.id, 'layer_fertilizer');
                                        else  gisMap.loadMap($node.id, $node.type);
                                    }
                                })
                            }

                        }
                    }
                    return tmp;
                }
            };
        }
    };

    module._bindToJsTree = function(data, callback){

        var $tree=$('.data');
        if($('.data').jstree()!=undefined){
            $('.data').jstree().destroy();
        }
        $tree.jstree({
            'core' : {
                'force_text' : true,
                'data' :data,
                'check_callback' : function (op, node, par, pos, more) {
                    if(op == 'rename_node'){
                        var sent=true;
                        if(node.text==pos) {
                            sent=false;
                        }
                        if(sent){
                            $.ajax({
                                url: window.base_url + '/folders/update-layer/'+node.id,
                                data: {
                                    folderId: node.id,
                                    name : pos.trim()
                                },
                                type:  'post',
                                success: function( data ){
                                    var errorMessage = buildMessage(data.message);
                                    var title = Lang.get('common.info_title');
                                    fancyMessage(errorMessage,title,function(){
                                        if(data.code == 403){
                                            window.location.href = window.permission_denined_url;
                                        }
                                        if(data.code == 401) {
                                            window.location.href = window.base_url + '/login';
                                        }else if(data.code==200) return;
                                        module.loadTree();
                                    });
                                }
                            })
                        }
                    }else if(op == 'delete_node'){
                        var totalFolderChecked = module._getTotalFolderSelect();
                        var totalLayerChecked = module._getTotalLayerSelect();
                        var isFolderSelected = true;
                        var selectedIds;
                        if (totalFolderChecked == 0 && totalLayerChecked == 0) {
                            fancyAlert(Lang.get('common.folder_edit_ids_required'),
                                Lang.get('common.error_title'));
                            return false;
                        }else if(totalLayerChecked > 0){
                            isFolderSelected = false;
                            selectedIds = module._getLayerSelected('all');
                        }else
                            selectedIds = module._getFolderSelected('all');
                        bootbox.dialog({
                            message : Lang.get('common.main_confirm_delete_action'),
                            title : Lang.get('common.info_title'),
                            buttons : {
                                danger : {
                                    label : Lang.get('common.yes'),
                                    className : "btn-primary",
                                    callback : function(){
                                        $.ajax({
                                            url: window.base_url + '/folders/delete-folders',
                                            data: {
                                                folderIds : selectedIds,
                                                isFolderSelected : isFolderSelected
                                            },
                                            type:  'post',
                                            success: function( data ){
                                                if(data.code == 401) {
                                                    window.location.href = window.base_url + '/login';
                                                }
                                                var errorMessage = buildMessage(data.message);
                                                if(data.code==200) {
                                                    gisTree.loadTree(function () {
                                                        var nodeId=$('.data').jstree().get_selected()[0];
                                                        if(nodeId!= undefined)
                                                            gisMap.loadMap(nodeId);
                                                    });
                                                }
                                                else{
                                                    fancyMessage(errorMessage,Lang.get('common.error_title'),function(){
                                                        if(data.code == 403){
                                                            window.location.href = window.permission_denined_url;
                                                        }
                                                        module.loadTree();
                                                    });
                                                }
                                            }
                                        })
                                    }
                                },
                                success : {
                                    label : Lang.get('common.no'),
                                    className : "btn-primary",
                                    callback: function(){
                                        $tree.jstree().refresh();
                                    }
                                }
                            }
                        });
                        return false;
                    }
                },
                multiple : false
            },
            "plugins" : [ "dnd", "contextmenu", "types","state" ],
            "contextmenu": module._getContextMenu(),
            "types" : module._getType()

        }).bind('move_node.jstree', function(e, data) {
            var target = e.delegateTarget;
            var anchor_id = $(target).attr('aria-activedescendant')+'_anchor';
            var li_target = $('li[aria-labelledby='+anchor_id+']');
            var is_folder = (data.node.parent == '#');
            new_order =  li_target.attr('data-order');

            var url =  window.base_url + '/folders/'+data.node.id
            var requestData = {order_number: new_order,  sortAble : true};
            var confirm = true;

            if(!is_folder){
                if(data.parent != data.old_parent){
                    if((data.node.type.indexOf("fertility")>1)&&($tree.jstree().get_node(data.node.parent).type=='folder_admin')){
                        fancyAlert(Lang.get('common.alert_move_fertility_map'),Lang.get('common.error_title'),'OK');
                        $tree.jstree('refresh');
                        confirm = false;
                    }
                    url = window.base_url + '/folders/change-folder';
                    requestData = {layerId : data.node.id,folderId : data.parent};
                }else{
                    if(data.old_position == data.position)
                        confirm = false;
                }
            }else{
                if(li_target.attr('aria-level') > 1){
                    new_order =  li_target.parents('li[aria-level=1]').attr('data-order');
                }
                if(data.old_position == data.position)
                    confirm = false;
            }


            if(confirm){
                bootbox.dialog({
                    message : Lang.get('common.confirm_action'),
                    title : Lang.get('common.info_title'),
                    onEscape: function() {
                        $tree.jstree().refresh();
                    },
                    buttons : {
                        danger : {
                            label : Lang.get('common.yes'),
                            className : "btn-primary",
                            callback : function(){
                                $.ajax({
                                    url: url,
                                    data: requestData,
                                    type:  'put',
                                    success: function( data ){
                                        reloadPage(data);
                                        if(data.code == 401)
                                            window.location.href = window.base_url+'/login';
                                        else if(data.code == 200)
                                            return;
                                        var errorMessage = buildMessage(data.message);
                                        fancyMessage(errorMessage,Lang.get('common.error_title'),function(){
                                            if(data.code == 403){
                                                window.location.href = window.permission_denined_url;
                                            }
                                            module.loadTree();
                                        });
                                    }
                                })
                            }
                        },
                        success : {
                            label : Lang.get('common.no'),
                            className : "btn-primary",
                            callback: function(){
                                $tree.jstree().refresh();
                            }
                        }
                    }
                });
            }
        })
            .bind('ready.jstree', function(){
                if(callback!=undefined)
                    callback.apply();
            })
            .on('click.jstree',function(event){
                $tree1=$('.data').jstree();
                $node=$tree1.get_node($tree1.get_selected());
                if((event.target.id!='')&&(event.target.id.indexOf($node.id)>(-1))) {
                    if ($node.type == 'layer_terrain') {
                        gisObject.layer_id = $node.id;
                        gisMap.defaultMap($node.id);
                    }else if(($node.type.indexOf('hidden')>1)){
                        gisMap.loadMap($node.id,$node.type);
                    } else if ($node.parent != "#") {
                        gisObject.layer_id = $node.id;
                        gisMap.loadMap($node.id,$node.type);
                        gisMap._removeCurrentInteraction();
                    }
                }
                else if($(event.target).attr('class')=='jstree-icon jstree-themeicon jstree-file jstree-themeicon-custom'
                    ||$(event.target).attr('class')=='jstree-icon jstree-themeicon glyphicon glyphicon-file jstree-themeicon-custom'){
                    if ($node.type == 'layer_terrain'||($node.type.indexOf('hidden')>1)) {
                        gisObject.layer_id = $node.id;
                        gisMap.loadMap($node.id,$node.type);
                    }
                    else if ($node.parent != "#") {
                        gisObject.layer_id = $node.id;
                        gisMap.loadMap($node.id,$node.type);
                        gisMap._removeCurrentInteraction();
                    }
                }
            });
        $tree.on("keydown", ".jstree-rename-input", function (e) {
            $('.jstree-rename-input').attr('maxLength', 100);
        });
    };

    module._getTotalFolderSelect = function() {
        var total_checked = 0;
        $('ul.jstree-container-ul > li[aria-level=1]').each(function() {
            if ($(this).attr('aria-selected') == "true")
                total_checked += 1;
        });

        return total_checked;
    };

    module._getTotalLayerSelect = function(){
        var total_checked = 0;
        $('ul.jstree-children > li[aria-level=2]').each(function() {
            if ($(this).attr('aria-selected') == "true")
                total_checked += 1;
        });

        return total_checked;
    };

    module._getFolderSelected = function(all) {
        var folders = $('ul.jstree-container-ul > li[aria-selected=' + true + '] ');
        if(typeof all === 'undefined'){
            return folders.attr('id');
        }
        var folderIds = [];
        folders.each(function(){
            folderIds.push($(this).attr('id'));
        })
        return folderIds;
    };

    module._getLayerSelected = function(all) {
        var folders = $('ul.jstree-children > li[aria-selected=' + true + '] ');
        if(typeof all === 'undefined'){
            return folders.attr('id');
        }
        var folderIds = [];
        folders.each(function(){
            folderIds.push($(this).attr('id'));
        })
        return folderIds;
    };

    module.loadTree = function( callback) {
        $.ajax({
            url:window.base_url +'/jsonTree',
            type:'POST',
            data : {
                isVisibleLayer : module.show_all,
                user_id: gisObject.user_id_main
            },
            async:false,
            cache:false,
            success: function(data) {
                reloadPage(data);
                module._bindToJsTree(data, callback);
            }
        });
    };


})(gisTree = {});
