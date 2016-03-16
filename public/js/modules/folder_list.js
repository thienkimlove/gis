$(function () {
    gisTree.loadTree();
    $('.btn-create-folder').click(function (event ) {
        var action = $(this).attr('data-action');
        $.fancybox([{
            href: window.base_url + '/admin/folders/' + action,
            type: 'ajax',
            helpers: {
                overlay: {
                    closeClick: false
                }
                // Disable click outside event
            }
        }], {
            afterLoad: function (data) {
                try {
                    var json = $.parseJSON(data.content);
                    fancyAlert(json.message, Lang.get('common.error_title'));
                    top.$.fancybox.close();
                    return false;
                } catch (err) {

                }
            }
        });
    });

    $('.btn-edit-folder').click(
        function (event) {
            event.preventDefault();

            var totalChecked = gisTree.getTotalFolderSelect();
            if (totalChecked == 0) {
                fancyAlert(Lang.get('common.folder_edit_ids_required'), Lang
                    .get('common.error_title'));
                return false;
            }

            if (totalChecked > 1) {
                fancyAlert(Lang.get('common.folder_edit_ids_max'), Lang
                    .get('common.error_title'));
                return false;
            }

            $('ul.jstree-container-ul > li ').each(function () {
                if ($(this).attr('aria-selected') == "true") {
                    var checkValue = $(this).attr('id');
                }

            });

            var checkValue = gisTree.getFolderSelected();

            $.fancybox([{
                href: window.base_url + '/admin/folders/' + checkValue
                + '/edit',
                type: 'ajax',
                helpers: {
                    overlay: {
                        closeClick: false
                    }
                    // Disable click outside event
                }
            }], {
                afterLoad: function (data) {
                    try {
                        var json = $.parseJSON(data.content);
                        fancyAlertAndLoadPage(json.message, Lang.get('common.error_title'));
                        top.$.fancybox.close();
                        return false;
                    } catch (err) {

                    }
                }
            });
        });
    $('.btn-delete-folder').click(
        function (event) {
            event.preventDefault();

            var totalFolderChecked = gisTree.getTotalFolderSelect();
            var totalLayerChecked = gisTree.getTotalLayerSelect();
            var isFolderSelected = true;

            if (totalFolderChecked == 0 && totalLayerChecked == 0) {
                fancyAlert(Lang.get('common.folder_edit_ids_required'),
                    Lang.get('common.error_title'));
                return false;
            } else if (totalLayerChecked > 0) {
                isFolderSelected = false;
                var selectedIds = gisTree.getLayerSelected('all');
            } else
                var selectedIds = gisTree.getFolderSelected('all');

            var token = $('input[name=_token]').val();
            bootbox.confirm(Lang.get('common.confirm_delete'), function (result) {
                if (result) {
                    $.ajax({
                        url: window.base_url + '/admin/folders/delete-folders',
                        data: {
                            folderIds: selectedIds,
                            isFolderSelected: isFolderSelected
                        },
                        type: 'post',
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', token);
                        },
                        success: function (data) {
                            var message = buildMessage(data.message);

                            if (data.code == 200) {
                                title = Lang.get('common.info_title');
                                fancyAlertAndLoadPage(message, Lang.get('common.info_title'));
                            } else {
                                if (data.code == 403) {
                                    fancyAlertAndLoadPage(message, Lang.get('common.error_title'));
                                } else {
                                    fancyAlert(message, Lang.get('common.error_title'));
                                }
                            }
                        }
                    })
                }
            });
        });
    $('.btn-upload-layer').click(function (event) {
        $.fancybox([{
            href: window.base_url + '/admin/import-data',
            type: 'iframe',
            maxHeight: 430,
            autoHeight: true,
            autoResize: true,
            scrolling: false,
            helpers: {
                overlay: {
                    closeClick : false
                }
            }
        }]);
    });

});