/**
 * Created by smagic39 on 9/3/2015.
 */
$(function(){
    $(document).on('click','#btn-update-edit-download',function(event){
        event.preventDefault();
        $('.frm-validation').validationEngine();
        gisForm.clickSave(null, {
            formEle: $('.form-download-update'),
            callbackFunction: function (data) {
                fancyMessage(data.message, window.info_title);
                closeFancy();
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
        });
        gisGrid.loadData();
    });
});

