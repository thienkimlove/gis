$(function () {
    gisGrid.init({
        contentEle: $("#jqGrid"),
        url: window.base_url + '/download-management/download-grid',
        colModel: [
            {
                label: Lang.get('common.form_downloadmanagement_id'),
                name: 'download_id',
                sortable: false,
                width: 100
            },
            {
                label: Lang.get('common.form_downloadmanagement_download_date_lbl'),
                name: 'download_date',
                sortable: false,
                align: 'left',
                width: 100,
                sortable:true
            },
            {
                label: Lang.get('common.form_downloadmanagement_user_code_lbl'),
                name: 'user_code',
                sortable: false,
                align: 'right',
                width: 75,
                formatter: function (cellvalue, options, rowObject) {
                    return '<div class="">'+cellvalue + '<div>';
                }
            },
            {
                label: Lang.get('common.form_downloadmanagement_user_name_lbl'),
                name: 'user_name',
                sortable: false,
                width: 75
            },
            {
                label: Lang.get('common.form_downloadmanagement_user_group_lbl'),
                name: 'user_group',
                sortable: false,
                width: 75
            },
            {
                label: Lang.get('common.form_downloadmanagement_mapname_lbl'),
                name: 'map_name',
                sortable: false,
                width: 75
            },
            {
                label: Lang.get('common.form_downloadmanagement_crop_lbl'),
                name: 'crop_map',
                sortable: false,
                width: 75
            },
            {
                label: Lang.get('common.form_downloadmanagement_mesh_size_lbl'),
                name: 'mesh_size',
                width: 75,
                align: 'right',
                sortable: false,
                formatter: function (cellvalue, options, rowObject) {
                    return cellvalue + 'm';
                }
            },
            {
                label: Lang.get('common.form_downloadmanagement_area_a_lbl'),
                name: 'area',
                width: 75,
                align: 'right',
                sortable: false,
                formatter: function (cellvalue, options, rowObject) {
                    return cellvalue + 'a';
                }
            },
            {
                label: Lang.get('common.form_downloadmanagement_unit_price_lbl'),
                name: 'unit_price',
                width: 75,
                align: 'right',
                sortable: false,
                formatter: function (cellvalue, options, rowObject) {
                    return cellvalue + Lang.get('common.total_amount_value');
                }

            },
            {
                label: Lang.get('common.form_downloadmanagement_price_lbl'),
                name: 'price',
                width: 75,
                align: 'right',
                sortable: false,
                formatter: function (cellvalue, options, rowObject) {
                    return cellvalue + Lang.get('common.total_amount_value');
                }

            },
            {
                label: Lang.get('common.form_downloadmanagement_payment_lbl'),
                name: 'payment',
                align: 'center',
                width: 60,
                formatter: checkBoxFormat,
                sortable: false
            },
            {
                label: Lang.get('common.form_downloadmanagement_remark_lbl'),
                name: 'remark',
                sortable: false
            }

        ],
        pager: "#jqGridPager"
    });
    gisGrid.loadData();
    var searhStatus = false;
    var    downloadModel = {
                        userName: '',
                        useCode:'',
                        userGroup: '',
                        downloadId:'',
                        downloadDateStart: '',
                        downloadDateEnd: '',
                        paymentState:''
                    };

    function checkBoxFormat(cellvalue, options, rowObject) {
        if (cellvalue) {
            return '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
        }
        return '<span class="glyphicon" aria-hidden="true"></span>';
    }

    $('#btn-show-edit-download').click(function (event) {
        event.preventDefault();
        //var totalChecked = config.checkeds.size();
        /*  gisForm.clickEdit({
         checkeds: $('div#holdChecked > input'),
         requireMessage: Lang.get('common.fertilization_price_edit_ids_max'),
         errorTitle: Lang.get('common.error_title'),
         requireMaxMessage: Lang.get('common.fertilization_price_edit_ids_max'),
         callbackReturnUrl: function (value) {
         return window.base_url + '/admin/download-management/edit/' + value;
         }
         });*/
        var statusCheck = $('#paymentState').val();
        var listInput = $('div#holdChecked > input');
        var totalChecked = listInput.length;

        if (statusCheck.indexOf(',') > 0 && listInput.length == 0) {
            if(listInput.length == 0)
                fancyAlert(Lang.get('common.select_min_required'), Lang.get('common.error_title'));
            return false;
        } else {
            var postData = "";
            $.each(listInput,function(index,val){
                postData += $(val).val()+",";
            });
            $.fancybox([{
                    href: window.base_url + '/admin/download-management/edit/' + postData,
                    type: 'ajax',
                    helpers: {
                        overlay: {closeClick: false} //Disable click outside event
                    }
                }], {
                    afterLoad: function (data) {
                        try {
                            var json = $.parseJSON(data.content);
                            bootbox.dialog({
                                message: json.message,
                                title: Lang.get('common.error_title'),
                                buttons: {
                                    success: {
                                        label: Lang.get('common.yes'),
                                        className: "btn-primary"
                                    }
                                }
                            });
                            setTimeout(function () {
                                reloadPage(data);
                            }, 3000);
                            top.$.fancybox.close();
                            gisGrid.refresh();
                            return false;
                        } catch (err) {

                        }
                    },
                    afterClose: function () {
                        //gisGrid.refresh();
                        $('div#holdChecked > input').remove();
                        $("#jqGrid").find('input[type=checkbox]').prop('checked',false).parents('tr').removeClass('ui-state-highlight').attr('aria-selected','false');
                    }
                }
            );

        }
    });


    $("#downloadDateStart").datepicker({
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        dateFormat: "yy-mm-dd"
    });
    $("#downloadDateEnd").datepicker({
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        dateFormat: "yy-mm-dd"
    });


    jQuery.download = function (url, data, method) {
        if (url && data) {
            data = typeof data == 'string' ? data : jQuery.param(data);
            var inputs = "<input name='_token' type='hidden' value='" + $('input[name=_token]').val() + ">";
            jQuery.each(data.split('&'), function () {
                var pair = this.split('=');
                inputs += '<input type="hidden" name="' + pair[0] + '" value="' + pair[1] + '" />';
            });
            jQuery('<form action="' + url + '" method="' + (method || 'post') + '">' + inputs + '</form>')
                .appendTo('body');
        }
        ;
    };
    $('#btn-show-export-csv').click(function (event) {
        $('.frm-validation').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    $('.frm-validation').validationEngine('hideAll');
                }, 4000);
                if (status === false) {
                    return false;
                } else {
                    $('#IFRAMEID').remove();
                    if(!downloadModel){
                      return fancyAlert(Lang.get('common.emptyrecords'),Lang.get('common.emptyrecords'));
                    }

                    $.post(window.base_url+'/admin/download-management/get-list-data-csv',
                        downloadModel,
                        function(data){
                            if( data.code == 0) {
                                 return fancyAlert(Lang.get('common.emptyrecords'),Lang.get('common.info_title'));
                            }

                                var iframe = document.createElement('iframe');
                                iframe.id = "IFRAMEID";
                                iframe.style.display = 'none';
                                document.body.appendChild(iframe);
                                iframe.src = $('.frm-validation').attr('action') + '?' + $.param(downloadModel);
                                iframe.addEventListener("load", function () {});

                    });


                }
            }
        });

        return true;
    });


    $('#btn-search-download').click(function (event) {
        $('.frm-validation').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    $('.frm-validation').validationEngine('hideAll');
                }, 4000);
                if (status === false) {
                    return false;
                } else {
                    event.preventDefault();
                    downloadModel = {
                        userName: function () {
                            return $('#userName').val();
                        },
                        useCode: function () {
                            return $('#useCode').val();
                        },
                        userGroup: function () {
                            return $('#userGroup').val();
                        },
                        downloadId: function () {
                            return $('#downloadId').val();
                        },
                        downloadDateStart: function () {
                            return $('#downloadDateStart').val();
                        },
                        downloadDateEnd: function () {
                            return $('#downloadDateEnd').val();
                        },
                        paymentState: function () {
                            return $('#paymentState').val();
                        },
                    };
                    $("#jqGrid").jqGrid('setGridParam', {search: true, postData: downloadModel, mtype: 'POST'}); // Post data for jqgrid
                    gisGrid.refresh(window.base_url + '/admin/download-management/search-download');
                    $('div#holdChecked > input').remove();
                }
            }
        });

    });
    $('#btn-reset-download').click(function (event){
        gisGrid.refresh();
        $('div#holdChecked > input').remove();

    });


// call the function
    function ajax_download(url, data) {
        var $iframe,
            iframe_doc,
            iframe_html;

        if (($iframe = $('#download_iframe')).length === 0) {
            $iframe = $("<iframe id='download_iframe'" +
                " style='display: none' src='about:blank'></iframe>"
            ).appendTo("body");
        }

        iframe_doc = $iframe[0].contentWindow || $iframe[0].contentDocument;
        if (iframe_doc.document) {
            iframe_doc = iframe_doc.document;
        }

        iframe_html = "<html><head></head><body><form method='POST' action='" +
            url + "'>"

        Object.keys(data).forEach(function (key) {
            console.log(data[key]);
            iframe_html += "<input type='hidden' name='" + key + "' value='" + data[key] + "'>";
        });

        iframe_html += "</form></body></html>";

        iframe_doc.open();
        iframe_doc.write(iframe_html);
        $(iframe_doc).find('form').submit();
        $(iframe_doc).remove();
    }


});

$(document).ready(function(){
    $('.ui-jqgrid-sortable').css({cursor:"default"});
    $('.ui-state-default').css({color: "#ffffff !important"});
});