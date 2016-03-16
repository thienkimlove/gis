
$(document).ready(function () {
    fancyboxPopup('.fancybox-list-btn');
    jqgridExample.loadGridData();

});
$('#data').jstree({
    'core' : {
        'data' : JSON.parse($('#json-folder-data').attr('data-meta')),
        'check_callback' : true
    },
    "plugins" : [
        "dnd", "search",
        "state", "types", "wholerow","checkbox"
    ],
    "types" : {
        "#" : {
            "max_depth" : 2,
            "valid_children" : ["folder"]
        },
        "folder" : {
            "valid_children" : [ "file" ],
            "max_depth": 1,
            "is_draggble" : false
        },
        "file" : {
            // the following three rules basically do the same
            "valid_children" : "none",
            "max_children" : 0,
            "max_depth" : 0,
            "icon" : "glyphicon glyphicon-file"
        }

    }
    }).on(
        'move_node.jstree',
        function (e, data) {
            $.post( window.base_url + '/upload-layer/process-export',
                { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position }
            ).fail(function () {
                    data.instance.refresh();
                })
            alert("Move file "+data.node.text +" to " + data.parent );

        })
(function(module){

    module.userModel = {
        mapName: '',
        userName: ''
    };
    module.loadGridData = function(){
        $("#jqGrid")
            .jqGrid({
                url: 'upload-layer/filter/'+JSON.stringify(module.userModel),
                datatype: "json",
                colModel: [
                    { label: Lang.get('common.label_frm_user_map_upload_map_name'), name: 'mapName', width: 75 },
                    { label: Lang.get('common.label_frm_user_map_upload_user_name'), name: 'userName', width: 75 }
                ],
                autowidth: true,
                viewrecords: true, // show the current page, data rang and total records on the toolbar
                width: Math.round($(window).width()) /2,
                height: Math.round($(window).height()) /3,
                rowNum: 20,
                loadonce: false,
                multiselect: true,
                pager: "#jqGridPager"
            });
        module.getParamSelect = function(idGrid) {
            var s;
            s = jQuery("#" + idGrid).jqGrid('getGridParam', 'selarrrow');

            return s;
        };
    };
    $(window).resize(
        function() {

            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }

            window.afterResize = setTimeout(function() {
                $("#jqGrid").jqGrid('setGridWidth',
                    jQuery(".ui-jqgrid").parent().width() - 2);
            }, 500);
        });






})(jqgridExample = {});


$('.btn-delete-usermap').click(
    function() {
        var totalChecked = jqgridExample.getParamSelect('jqGrid');
        if (totalChecked.length == 0) {
            fancyAlert(Lang.get('common.user_map_select_rows'), 'Error');
            return false;
        }
        var $serialize = serialize(totalChecked);
        bootbox.dialog({
            message : Lang.get('common.user_map_confirm_rows'),
            title : Lang.get('common.user_map_title_delete_rows'),
            buttons : {
                success : {
                    label : "Cancel",
                    className: "btn-primary"
                },
                danger : {
                    label : "Delete",
                    className : "btn-danger",
                    callback : function() {
                        $('#user_delete_list_data').val($serialize);
                        $('#frm-upload-layer').submit();
                    }
                }

            }
        });

    });

function serialize(mixed_value) {

    var val, key, okey,
        ktype = '',
        vals = '',
        count = 0,
        _utf8Size = function(str) {
            var size = 0,
                i = 0,
                l = str.length,
                code = '';
            for (i = 0; i < l; i++) {
                code = str.charCodeAt(i);
                if (code < 0x0080) {
                    size += 1;
                } else if (code < 0x0800) {
                    size += 2;
                } else {
                    size += 3;
                }
            }
            return size;
        };
    _getType = function(inp) {
        var match, key, cons, types, type = typeof inp;

        if (type === 'object' && !inp) {
            return 'null';
        }
        if (type === 'object') {
            if (!inp.constructor) {
                return 'object';
            }
            cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);
            if (match) {
                cons = match[1].toLowerCase();
            }
            types = ['boolean', 'number', 'string', 'array'];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    type = _getType(mixed_value);

    switch (type) {
        case 'function':
            val = '';
            break;
        case 'boolean':
            val = 'b:' + (mixed_value ? '1' : '0');
            break;
        case 'number':
            val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
            break;
        case 'string':
            val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
            break;
        case 'array':
        case 'object':
            val = 'a';

            for (key in mixed_value) {
                if (mixed_value.hasOwnProperty(key)) {
                    ktype = _getType(mixed_value[key]);
                    if (ktype === 'function') {
                        continue;
                    }

                    okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                    vals += this.serialize(okey) + this.serialize(mixed_value[key]);
                    count++;
                }
            }
            val += ':' + count + ':{' + vals + '}';
            break;
        case 'undefined':
        default:
            val = 'N';
            break;
    }
    if (type !== 'object' && type !== 'array') {
        val += ';';
    }
    return val;
}