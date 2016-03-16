$(function() {
    gisGrid.init({
        contentEle: $("#jqGrid"),
        url: window.base_url + '/admin/get-grid-data-price',
        colModel: [{
            label: Lang.get('common.fertilization_price_label_start_date'),
            name: 'start_date',
            width: 75,
            sortable: false,
            formatter : 'date',
            formatoptions : {newformat : 'Y-m-d'}
        }, {
            label: Lang.get('common.fertilization_price_label_end_date'),
            name: 'end_date',
            width: 75,
            sortable: false,
            formatter : 'date',
            formatoptions : {newformat : 'Y-m-d'}
        }, {
            label: Lang.get('common.fertilization_price_label_price'),
            name: 'price',
            width: 75,
            align : 'right',
            sortable: false
        }],
        pager: "#jqGridPager"
    });

    gisGrid.loadData();

    $('.btn-show-create-price').click(function(event){
        $.fancybox( [{
                href : window.base_url + '/admin/fertilizationprice/create',
                type : 'ajax',
                helpers: {
                    overlay: { closeClick: false } //Disable click outside event
                }
            }], {
                afterLoad: function(data){
                    reloadPage(data);
                    try {
                        var json = $.parseJSON(data.content);
                        bootbox.dialog({
                            message : json.message,
                            title : Lang.get('common.error_title')
                        });
                        top.$.fancybox.close();
                        return false;
                    } catch(err) {
                    }
                },
                afterClose : function(){
                    gisGrid.refresh();
                    $('div#holdChecked > input').remove();
                }
            }
        );
    });

    $('.btn-show-edit-price').click(function(event){
        event.preventDefault();
        gisForm.clickEdit({
            checkeds : $('div#holdChecked > input'),
            requireMaxMessage : Lang.get('common.select_max'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            formEle : $('.frm-validation-list-price'),
	        callbackReturnUrl : function(value) {
	            return window.base_url + '/admin/fertilizationprice/' + value + '/edit'
	        }
        });
    });

    $('.btn-show-delete-price').click(function(event){
        gisForm.clickDelete(event, {
            checkeds : $('div#holdChecked > input'),
            requireMessage : Lang.get('common.select_min_required'),
            errorTitle : Lang.get('common.error_title'),
            formEle : $('.frm-validation-list-price'),
            callbackFunction : function(data){
                reloadPage(data);
                if (data.code == 401) {
                    window.location.href = window.base_url + 'login';
                }
                if (typeof data.redirect !== 'undefined') {
                    if (data.redirect !== null) {
                        var url = data.redirect;

                        if (url.length > 0) {
                            location.href = data.redirect;
                        }
                    }
                }

                var title = 'common.error_title';
                if (data.code == 200) {
                    $('div#holdChecked > input').remove();
                    title = 'common.info_title';
                    gisGrid.refresh(window.base_url + '/fertilization-price/afterDelete');
                }
                fancyAlert(buildMessage(data.message), Lang.get(title));

            }
        });

    });

});
$(document).ready(function(){
    $('.ui-jqgrid-sortable').css({cursor:"default"});
    $('.ui-state-default').css({color: "#ffffff !important"});
});