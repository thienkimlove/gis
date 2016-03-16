(function (module) {

    module.showing = false;
    module.noneAlert =-1;
    module.MAP_EXIST = -2;
    module.openChangingColor = function () {
        var fertilizerId = gisObject.layer_id;
        gisForm.openPopup2(window.base_url + '/changing-color/' + fertilizerId);
    };
    module.callback = function (){
      return false;
    };
    module.openChangingPopup =  function(url,postData,callBack){
        var ajaxType = 'POST';
        if( url == undefined ) {
            return null;
        }
        if(postData == undefined) {
            ajaxType = 'GET';
            postData = [];
        }

        $.ajax({
            url: url,
            type:ajaxType,
            cache:false,
            data:postData,
            success: function (data) {
                // Show error message
                if(data.message !== undefined){
                    fancyMessage(data.message,Lang.get('common.info_title'));
                    return;
                }
                // Show popup

                $.fancybox(data, {
                    // fancybox API options
                    wrapCSS:'merging-map',
                    fitToView: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: { closeClick: false } //Disable click outside event
                    },
                    afterClose : function(){
                        if(callBack) callBack();
                    },
                    tpl:{
                        closeBtn :'<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" onclick="changingcolor.openChangingColor();return true;"></a>'
                    }
                }); // fancybox
                $.fancybox.update();
            }
        });
    };
    module.openPopupMergeData =  function(url,postData){
        var ajaxType = 'POST';
        if( url == undefined ) {
            return null;
        }
        if(postData === undefined) {
            ajaxType = 'GET';
            postData = [];
        }
        $.ajax({
            url: url,
            type:ajaxType,
            cache:false,
            data:postData,
            success: function (data) {
                // Show error message
                if(data.message !== undefined){
                    fancyMessage(data.message,Lang.get('common.info_title'));
                    return;
                }
                // Show popup

                $.fancybox(data, {
                    // fancybox API options
                    wrapCSS:'merging-map',
                    fitToView: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: { closeClick: false } //Disable click outside event
                    },
                    afterClose : function(){
                        changingcolor.openMergingColor();
                    },
                    tpl:{
                        closeBtn :'<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" onclick="changingcolor.openMergingColor(); return true;"></a>'
                    }
                }); // fancybox
                $.fancybox.update();
            }
        });
    };
    module.openValueChangingColor = function () {
        var lis = $('#colorList').find('.color-selected');
        if (lis.length != 1) {
            return fancyMessage(Lang.get('common.empty_color_selection'),Lang.get('common.info_title'));
        }
        var colorBoxs = $('#colorList .color-selected').find('.color-box');
        var valueList = $(lis).attr('value') != '' ? $(lis).attr('subvalue') : '' ;
        var listValues = valueList.split(',');
        var obj = {
            'color': $(lis).attr('color'),
            'layerID': gisObject.layer_id,
            'rgbCode': $(colorBoxs).css("background-color").match(/\d+/g),
            'main':$('#changingcolor_main_barrel').val(),
            'sub': $('#changingcolor_sub_barrel').val(),
        };
        module.openChangingPopup(window.base_url + '/open-value-changing-color',obj);

    };
    module.submitChangingColor = function () {
        gisObject.returnMap = true;
        $('#hidden_update_colors').val(null);
        gisForm.clickSave(null, {
            formEle: $('.changing-color-frm'),
            callbackFunction: function (data) {
                if(data.code == module.MAP_EXIST){
                    showConfirm(data.message, window.info_title,function(){
                        $('#hidden_update_colors').val(true);
                        gisForm.clickSave(null, {
                            formEle: $('.changing-color-frm'),
                            callbackFunction:function(){
                                fancyMessage(Lang.get('common.save_success'), window.info_title);
                                module.openChangingColor();
                            }
                        });
                    });
                }else{
                    fancyMessage(data.message, window.info_title);
                    module.openChangingColor();
                }

            }
        });

    };

    module.submitEditingColor = function () {
        $('#hidden_update_colors').val(null);
        gisForm.clickSaveEditingColor(null, {
            formEle: $('.editing-color-frm'),
            callbackFunction: function (data) {
                if(data.code == module.MAP_EXIST){
                    showConfirm(data.message, window.info_title,function(){
                        $('#hidden_update_colors').val(true);
                        gisForm.clickSaveEditingColor(null, {
                            formEle: $('.editing-color-frm'),
                            callbackFunction:function(){
                                fancyMessage(Lang.get('common.save_success'), window.info_title);
                                closeFancy();
                            }
                        });
                    });
                }else{
                    fancyMessage(data.message, window.info_title);
                    closeFancy();
                }
            }
        });
    };
    module.editColorClient = function () {

        var mainValue = parseInt('0' + $("#changingcolor_main_barrel").val());
        var subValue = parseInt('0' + $("#changingcolor_sub_barrel").val());
        var value = mainValue + "," + subValue;
        $('#colorList').find('.color-selected').attr("subvalue", value);
        var name;
        if($('#hidden_is_one_barrel').val() == ''){
            name = Lang.get('common.changingcolor_main_barrel') + ' ' + mainValue ;
            name+=  ' ' + Lang.get('common.changingcolor_sub_barrel') + ' ' + subValue;
        }else
            name = mainValue;
        $('#colorList').find('.color-selected').find('.color-name').text('').text(name);

    };
    // End changing color

    module.submitAreaColor = function(){
            var listSelect = $('#listSelect');
            var current = $('#listSelect').find('.selected');
            if(current.length !== 1){
               bootbox.alert('Please select one item !');
               return false;
            }
        var data = {
            layerID: gisObject.layer_id,
            mapInfoIds: gisObject.map_info_ids,
            currentID:  current.attr('color'),
        };
        //
        gisObject.returnMap = true;
        gisForm.openPopup2(window.base_url + '/submit-merging-map-color-map', data,closeFancyMap());

    };
    module.openMergingColor = function () {
        var data = {
            fertilizerId: gisObject.layer_id,
            mapInfoIds: gisObject.map_info_ids,
        };

        gisForm.openPopup2(window.base_url + '/merging-map-color-map', data);


    };
    module.mergeDataMapColor = function () {
        var data = {
            fertilizerId: gisObject.layer_id,
            mapInfoIds: gisObject.map_info_ids,
        };
        module.openPopupMergeData(window.base_url + '/merging-other-color-map', data);
    };
    module.openEditingColor = function (layerId) {
        var valid = gisForm.validateForm('frm-validation');
        if(!valid){
            return true;
        }
        var data = {
            layerId: gisObject.layer_id,
            mapInfoIds: gisObject.map_info_ids,
            main_barrel: $('#mergingColor #main_barrel').val(),
            sub_barrel:$('#mergingColor #sub_barrel').val(),
            colorCode:$('#mergingColor #colorCode').val(),
            isOneBarrel:$('#mergingColor #isOneBarrel').val(),
        };
        gisObject.returnMap = true;
        gisForm.openPopup2(window.base_url + '/editing-color',data);
        module.openMergingColor();
    };
    module._openEditingColor = function () {

        //var fertilizerId = $('#fertilizerId').val();
        var fertilizerId = gisObject.layer_id;
        var group = $("#color_list_merging");
        $('#colorSelectIds').val(gisObject.map_info_ids);
        var list = group.find('.selected');
        if (list.length === 0) {
            fancyMessage(Lang.get('common.select_items_required'), window.info_title);
            return;
        }
        var listColors = Array();
        for (var i = 0; i < list.length; i++) {
            listColors.push(list[i].attributes.color.value)
        }
        var colorIds = listColors.toString();
        gisForm.openPopup(window.base_url + '/editing-color/' + fertilizerId + '/' + colorIds + '/' + gisObject.map_info_ids,
            function () {
                module.mergeDataMapColor();
            }
        );
    };

    module.showListColors = function () {

        var display = $(".editingcolor-list").css("display");
        if (display === 'block') {
            module.hideListColors();
            return;
        }

        $(".editingcolor-list").css("display", "block");
        $(".editingcollor-button-group").css("display", "none");
        module.showing = true;
    };

    module.hideListColors = function () {
        var display = $(".editingcolor-list").css("display");
        if (display === 'block' && !module.showing) {
            $(".editingcolor-list").css("display", "none");
            $(".editingcollor-button-group").css("display", "block");
        }
        module.showing = false;
    };
    module.colorPickColor = function (element) {
        $(element).colorpicker().on('changeColor.colorpicker', function (event) {
            var color = event.color.toRGB();
            $(element).attr('style', 'background:' + event.color.toHex());
            $('#colorCode').val(color.r + ',' + color.g + ',' + color.b);
        });


    };
    module.listSelected = function () {

        $(document).on("click", "#colorList li", function (e) {
            var parent = $(this).parent();
            parent.find('li').removeClass("color-selected");
            $(this).addClass('color-selected');
            var list = $(this).attr("subvalue").split(',');
            $("#changingcolor_main_barrel").val(list[0]);
            $("#changingcolor_sub_barrel").val(list[1]);
            $('#hidden_current_colors').val($(this).attr('color'));


        })
    };
    module.init = function () {
        module.listSelected();
    };


})(changingcolor = {});
$(document).ready(function () {

    changingcolor.init();
});
