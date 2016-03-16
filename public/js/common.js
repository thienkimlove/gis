/**
 * Created by haph1 on 7/20/2015.
 */
function isTextInput(node) {
    return ['INPUT', 'TEXTAREA'].indexOf(node.nodeName) !== -1;
}
document.addEventListener('touchstart', function (e) {
    if (!isTextInput(e.target) && isTextInput(document.activeElement)) {
        document.activeElement.blur();
    }
}, false);
function buildMessage(input) {
    var errorMessage = '';
    if (typeof input !== 'undefined') {
        if (input !== null) {

            if (typeof input === 'string') {
                errorMessage = input;
            } else {
                for (i = 0; i < input.length; i++) {
                    errorMessage += input[i] + "<br />";
                }
            }

        }
    }

    return errorMessage;
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function checkCookie(name) {
    var user = getCookie(name);
    if (user != "") {
       return true;
    }
    return false;
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function fancyAlertCallback(msg, callback) {
    bootbox.alert(msg, callback);
}

function fancyAlertAndLoadPage(msg, title) {
    bootbox.dialog({
        message: msg,
        title: title,
        buttons: {
            success: {
                label: Lang.get('common.button_alert_ok'),
                callback: function () {
                    location.reload();
                }
            }
        }
    });
}

function fancyAlert(msg, title, titleBtn) {
    if (typeof titleBtn === 'undefined')
        titleBtn = 'OK';
    bootbox.dialog({
        message: msg,
        title: title,
        buttons: {
            success: {
                label: titleBtn
            }
        }
    });
}

function fancyMessage(msg, title, callbackFunction) {
    bootbox.dialog({
        message: msg,
        title: title,
        buttons: {
            success: {
                label: Lang.get('common.button_alert_ok'),
                callback: function () {
                    if (typeof callbackFunction !== 'undefined')
                        callbackFunction();
                }
            }
        },
        onEscape: function (result) {

            if (typeof callbackFunction !== 'undefined')
                callbackFunction();
        }
    });
}

function fancyConfirm(msg, title) {
    bootbox.confirm(msg, function (result) {
        return result;
    });
}

function fancyAlertAfterClose(msg, title, code) {
    jQuery.fancybox({
        'modal': true,
        'content': "<div class='alert-wrap'><div class='alert-title'>" + title + "<span class='mini-close-button'><input class=\"mini-close-button\" type=\"button\" onclick=\"jQuery.fancybox.close();\" value=\"&times;\"></span></div><div class='msg-alert'>" + msg + "</div><div class='alert-footer'><input class=\"white-button\" type=\"button\" onclick=\"jQuery.fancybox.close();\" value=\"OK\"></div></div>",
        'afterClose': function () {
            if (code != 200)
                $('.btn-show-create-user').trigger('click');
        },
    });
}

function fancyConfirm(msg, callback) {
    var ret;
    jQuery.fancybox({
        modal: true,
        content: "<div style=\"margin:1px;width:240px;\">" + msg + "<div style=\"text-align:right;margin-top:10px;\"><input id=\"fancyConfirm_cancel\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"Cancel\"><input id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"OK\"></div></div>",
        onComplete: function () {
            jQuery("#fancyConfirm_cancel").click(function () {
                ret = false;
                jQuery.fancybox.close();
            })
            jQuery("#fancyConfirm_ok").click(function () {
                ret = true;
                jQuery.fancybox.close();
            })
        },
        onClosed: function () {
            callback.call(this, ret);
        }
    });
}
//"{"message":"Unauthorized.","code":401,"redirect":null,"data":null}"
function reloadPage(data) {
    try {
        var json = $.parseJSON(data.content);
        var haystack = [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417];
        for (var i = 0; i < haystack.length; i++) {
            if (haystack[i] == json.code) {
                top.$.fancybox.close();
                fancyMessage(json.message, Lang.get('common.error_title'), function () {
                    window.location.href = window.base_url + '/reload';
                    return true;
                });
            }
        }
        return false;
    } catch (err) {

    }
}
function showConfirm(message, title, callBackFunction) {

    bootbox.dialog({
        message: message,
        title: title,
        buttons: {
            danger: {
                label: Lang.get('common.yes'),
                className: "btn-primary",
                callback: function (data) {
                    if (callBackFunction !== undefined) callBackFunction(data);
                }
            },
            success: {
                label: Lang.get('common.no'),
                className: "btn-primary",
            }
        }
    });
};

//popup fancybox
function fancyboxPopup(selector) {
    $(selector).fancybox({
        helpers: {
            overlay: {
                closeClick: false
            }
        },
        afterLoad: function (response, previous) {
            if (IsJsonString(response.content)) {
                var dataResponse = JSON.parse(response.content);
                var message = buildMessage(dataResponse.message);
                if (message !== '') {
                    fancyAlert(message, 'Message');
                }
                if (dataResponse.code !== 200) {
                    jQuery.fancybox.close();
                    return false;
                }
            }
        }
    });
}


function gridResponsiveDesign() {
    // Code Reponsive Design Grid - Start
    jQuery("#jqGrid").jqGrid('setGridWidth',jQuery(".ui-jqgrid").parent().width() - 2);
    // On Resize
    $(window).resize(
        function () {
            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }
            window.afterResize = setTimeout(function () {
                jQuery("#jqGrid").jqGrid('setGridWidth',
                    jQuery(".ui-jqgrid").parent().width() - 2);
            }, 500);

        });

    // Code Reponsive Design Grid - End
}

var gisForm = {
    clickEdit: function (config) {
        //var totalChecked = config.checkeds.size();
        var totalChecked = config.checkeds.length;
        if (totalChecked == 0) {
            fancyAlert(config.requireMessage, config.errorTitle);
            return false;
        } else if (totalChecked > 1) {
            fancyAlert(config.requireMaxMessage, config.errorTitle);
            return false;
        } else {
            var checkValue = config.checkeds.val();
            $.fancybox([{
                    href: config.callbackReturnUrl(checkValue),
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
                    }
                }
            );
        }
    },
    openPopup: function (url, callBack, postData) {
        var ajaxType = 'POST';
        if (postData === undefined) {
            ajaxType = 'GET';
        }

        $.ajax({
            url: url,
            type: ajaxType,
            data: postData,
            success: function (data) {

                // Show error message
                if (data.message !== undefined) {
                    fancyMessage(data.message, Lang.get('common.info_title'));
                    return;
                }

                // Show popup

                $.fancybox(data, {
                    // fancybox API options

                    fitToView: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: {closeClick: false} //Disable click outside event
                    },
                    afterClose: function () {
                        setTimeout(function(){
                            if (callBack) callBack();
                        },50);

                    }
                }); // fancybox
            }
        });
    },
    openPopup2: function (url, postData, callBack) {
        var ajaxType = 'POST';
        if (url == undefined) {
            return null;
        }
        if (postData == undefined) {
            ajaxType = 'GET';
            postData = [];
        }

        $.ajax({
            url: url,
            type: ajaxType,
            cache: false,
            async: false,
            data: postData,
            success: function (data) {
                // Show error message
                if (data.message !== undefined) {
                    fancyMessage(data.message, Lang.get('common.info_title'));
                    return;
                }
                // Show popup

                $.fancybox(data, {
                    // fancybox API options
                    wrapCSS: 'merging-map',
                    fitToView: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    helpers: {
                        overlay: {closeClick: false} //Disable click outside event
                    },
                    afterClose: function () {
                        if (callBack) callBack();
                    },
                    tpl: {
                        closeBtn: '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" onclick="closeMergingMap();return true;"></a>'
                    }
                }); // fancybox
                $.fancybox.update();

            }
        });
    },
    clickDelete: function (event, config) {
        event.preventDefault();
        var totalChecked = config.checkeds.size();
        if (totalChecked == 0) {
            fancyAlert(config.requireMessage, config.errorTitle);
            return false;
        } else {
            bootbox.dialog({
                message: totalChecked + Lang.get('common.confirm_delete'),
                title: Lang.get('common.alert_title_message'),
                buttons: {
                    danger: {
                        label: Lang.get('common.yes'),
                        className: "btn-primary",
                        callback: function () {
                            submitAjaxRequest(config.formEle, event, config.callbackFunction);
                        }
                    },
                    success: {
                        label: Lang.get('common.no'),
                        className: "btn-primary"
                    }
                }
            });
        }
    },
    clickDeleteItems: function (event, config) {
        event.preventDefault();
        var totalChecked = config.checkeds.length;
        if (totalChecked == 0) {
            fancyAlert(config.requireMessage, config.errorTitle);
            return false;
        } else {
            bootbox.dialog({
                message:  totalChecked  + Lang.get('common.confirm_delete'),
                title: Lang.get('common.alert_title_message'),
                buttons: {
                    danger: {
                        label: Lang.get('common.yes'),
                        className: "btn-primary",
                        callback: function () {
                            submitAjaxRequest(config.formEle, event, config.callbackFunction);
                        }
                    },
                    success: {
                        label: Lang.get('common.no'),
                        className: "btn-primary"
                    }
                }
            });
        }
    },
    clickSave: function (event, config) {

        return config.formEle.validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    config.formEle.validationEngine('hideAll');
                }, 4000);
                if (status === false) {
                    return false;

                } else {
                    submitAjaxRequest(config.formEle, event, config.callbackFunction);
                }
            }
        });
    },
    clickSaveChangingUser: function (event, config) {
        return config.formEle.validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    config.formEle.validationEngine('hideAll');
                }, 4000);
                if (status === false) {
                    return false;

                } else {

                    var email = $("#email").val();
                    if (email !== '' && email !== window.email) {
                        showConfirm(Lang.get('common.changinguser_message_comfirm_save'), window.info_title, function (data) {
                            submitAjaxRequest(config.formEle, event, config.callbackFunction);
                        });
                    } else {
                        submitAjaxRequest(config.formEle, event, config.callbackFunction);
                    }
                }
            }
        });
    },
    clickSaveEditingColor: function (event, config) {
        return config.formEle.validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    config.formEle.validationEngine('hideAll');
                }, 4000);
                if (status === false) {
                    return false;

                } else {
                    var currentColor = $('#editingcolor-box').css("background-color");
                    if (currentColor === " " || currentColor == null) {
                        fancyMessage(Lang.get('common.editingcolor_color_required'), window.info_title);
                        return;
                    }
                    submitAjaxRequest(config.formEle, event, config.callbackFunction);
                }
            }
        });
    },
    validateForm: function (formClassName) {

        var theForm = $('.' + formClassName);
        var result;

        theForm.validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    theForm.validationEngine('hideAll');
                }, 4000);

                result = status;
            }
        });

        return result;
    }
};
//grid Class
var gisGrid = {
    contentEle: null,
    url: null,
    colModel: [],
    pager: null,
    init: function (config) {
        this.contentEle = config.contentEle;
        this.url = config.url;
        this.colModel = config.colModel;
        this.pager = config.pager;
    },
    loadData: function (screen) {
        var contentEle = this.contentEle;
        this.contentEle.jqGrid({
            url: this.url,
            datatype: "json",
            colModel: this.colModel,
            viewrecords: true, // show the current page, data rang and
            // total records on the toolbar
            autowidth: true, // responsive design grid need this property
            shrinkToFit: true, // responsive design grid need this property
            height: 300,
            loadonce: false,
            multiselect: true,
            autoencode: true,
            sortname: "username", // set default sort for only 'user register' screen
            rowNum: $('#item_perpage').val(),
            rowList: [10, 20, 30],
            pager: this.pager,
            onSelectRow: function (id) {
                contentEle.find('tr[id=' + id + '] input:checkbox').trigger('change');
            },
            gridComplete: function () {
                gridResponsiveDesign();
                contentEle.find('input[class=cbox]:checkbox').each(function () {
                    var id = $(this).parents('tr').attr('id');
                    var holder = id + 'holder';
                    if ($('div#holdChecked input[id=' + holder + ']').length > 0) {
                        $(this).prop('checked', true);
                    }
                    $(this).bind('change', function () {
                        if ($(this).is(':checked') && $('div#holdChecked input[id=' + holder + ']').length == 0) {
                            $('<input id="' + holder + '" name="ids[]" value="' + id + '" >').appendTo($('div#holdChecked'));
                        }
                        if (!$(this).is(':checked') && $('div#holdChecked input[id=' + holder + ']').length > 0) {
                            $('div#holdChecked > input[id=' + holder + ']').remove();

                        }
                    });
                });
                $('input#cb_jqGrid').bind('change', function () {
                    var check = $(this).is(':checked');
                    contentEle.find('input[class=cbox]:checkbox').each(function () {
                        var id = $(this).parents('tr').attr('id');
                        var holder = id + 'holder';

                        if (check && $('div#holdChecked input[id=' + holder + ']').length == 0) {
                            $('<input id="' + holder + '" name="ids[]" value="' + id + '" >').appendTo($('div#holdChecked'));
                        }
                        if (!check && $('div#holdChecked input[id=' + holder + ']').length > 0) {
                            $('div#holdChecked > input[id=' + holder + ']').remove();
                        }

                    });
                });
            },
            loadComplete: function (data) {

                if (screen === 'group_list') {

                    var grid = $(this);
                    var ids = $(this).jqGrid("getDataIDs");
                    var i, rowid;
                    $.ajax({
                        url: window.base_url + '/get-admin-group',
                        type: "POST",
                        dateType: "json",
                        success: function (data) {
                            for (i = 0; i < ids.length; i++) {
                                for (j = 0; j < data.length; j++) {
                                    if (data[j] == ids[i]) {
                                        rowid = ids[i];
                                        $('#' + $.jgrid.jqID(rowid)).addClass('admin-standard');
                                    }
                                }
                            }
                        }
                    });

                }
            },
            onPaging: function (pgButton) {
                // if user has entered page number
                if (pgButton == "user") {
                    // find out the requested and last page
                    var requestedPage = $('#input_jqGridPager > input:first').val();
                    var lastPage = $(this).getGridParam('lastpage');
                    // if the requested page is higher than the last page value
                    if (parseInt(requestedPage) > parseInt(lastPage)) {
                        // set the requested page to the last page value - then reload
                        $(this).trigger("reloadGrid", [{page: lastPage}]);
                    }
                }
            }
        });
    },
    refresh: function (url) {
        this.contentEle.jqGrid('setGridParam', {
            url: (!url) ? this.url : url,
            page: 1
        }).trigger("reloadGrid");
    }
};

function getFile() {
    document.getElementById("upfile").click();
}
function getHelp($popup) {
    $.post(window.base_url + "/get/help", {url: window.location.href, popup: $popup}, function (data) {
        if (data.code == 404) {
            fancyAlert(data.message, window.info_title);
        }
        else {
            window.open(data['url'], 'newwin', 'left=20,top=20,width=700,height=650,toolbar=1,resizable=0');
        }
    });
}

function isOnline() {
    return true;
    if (!navigator.onLine) {
        fancyAlert(Lang.get('common.unable_network'), window.info_title);
        return false;
    }
    return true;
}

function TextDiff(first, second) {
    var start = 0;
    while (start < first.length && first[start] == second[start]) {
        ++start;
    }
    var end = 0;
    while (first.length - end > start && first[first.length - end - 1] == second[second.length - end - 1]) {
        ++end;
    }
    end = second.length - end;
    return second.substr(start, end - start);
};

function ExecuteRegexExpression() {

    // Alpha numeric regex
    var alphaNumericRegex = new RegExp('^[a-zA-Z0-9]+$');

    $(document).on('paste', '.onlyAlphaNumeric', function () {
        var self = $(this);
        var orig = self.val();
        setTimeout(function () {
            var pasted = TextDiff(orig, $(self).val());
            if (!alphaNumericRegex.test(pasted)) {
                $(self).val(orig);
            }
        });
    });

    $(document).on('keypress', '.onlyAlphaNumeric', function (e) {
        // backspace
        if (e.which == 8) return true;
        if (e.which == 13) return true;
        //if (e.which == 64) return true;//64:@
        if (e.keyCode === 9) return true;
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (alphaNumericRegex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    // Numeric regex
    var numericRegex = new RegExp('^[0-9]*$');
    $(document).on('input', '.onlyNumeric', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();
        var currentText = TextDiff(oldValue, newValue);
        if (!numericRegex.test(currentText)) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    $(document).on('input', '.onlyNumeric9 input', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (newValue.length > 9) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!numericRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    // Numeric regex
    var decimalRegex = new RegExp('^[0-9.]*$');
    $(document).on('input', '.onlyDecimal6_2', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array[0].length > 4) isValid = false;
        if (array.length > 1 && array[1].length > 2) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!decimalRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });
    //Chi co 1 chu so thap phan sau dau phay
    $(document).on('input', '.onlyDecimal6_1', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array.length > 1 && array[1].length > 1) isValid = false;

        if (!isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    //chi nhap so nguyen duong có 1 chu so thap phan sau dau phay
    $(document).on('input', '.onlyDecimaPositive_1', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array.length > 1 && array[1].length > 1) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!decimalRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    $(document).on('input', '.onlyDecimal4_2 input', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array[0].length > 2) isValid = false;
        if (array.length > 1 && array[1].length > 2) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!decimalRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });
    $(document).on('input', '.onlyDecimal3_1 input', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array[0].length > 2) isValid = false;
        if (array.length > 1 && array[1].length > 1) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!decimalRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    $(document).on('input', '.smallint, .smallint input', function (event) {

        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();
        var currentText = TextDiff(oldValue, newValue);
        if (!numericRegex.test(currentText) || newValue.length > 4) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });
    $(document).on('input', '.grid-int4 input', function (event) {

        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();
        var isValid = true;
        var array = newValue.split(".");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array[0].length > 3) isValid = false;
        if (array.length > 1 && array[1].length > 1) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!decimalRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });

    $(document).on('input', '.text200 input', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();
        if (newValue.length > 200) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });
    // Email regex
    var emailRegex = new RegExp('^[a-z@-Z0-9._-]+$');

    $(document).on('paste', '.onlyEmail', function () {
        var self = $(this);
        var orig = self.val();
        setTimeout(function () {
            var pasted = TextDiff(orig, $(self).val());
            if (!emailRegex.test(pasted)) {
                $(self).val(orig);
            }
        });
    });

    $(document).on('keypress', '.onlyEmail', function (e) {
        // backspace
        if (e.which == 8) return true;
        if (e.which == 13) return true;
        //if (e.which == 64) return true;//64:@
        if (e.keyCode === 9) return true;
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (emailRegex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    // Text regex
    var textRegex = new RegExp('^([a-zA-Z0-9._-]|[\ ])+$');

    $(document).on('paste', '.onlyText', function () {
        var self = $(this);
        var orig = self.val();
        setTimeout(function () {
            var pasted = TextDiff(orig, $(self).val());
            if (!textRegex.test(pasted)) {
                $(self).val(orig);
            }
        });
    });

    $(document).on('keypress', '.onlyText', function (e) {
        // backspace
        if (e.which == 8) return true;
        if (e.which == 13) return true;
        //if (e.which == 64) return true;//64:@
        if (e.keyCode === 9) return true;
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (textRegex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    // Alpha numeric regex
    var passwordRegex = new RegExp('^[!-~]+$');
    $(document).on('paste', '.onlyPassword', function () {
        var self = $(this);
        var orig = self.val();
        setTimeout(function () {
            var pasted = TextDiff(orig, $(self).val());
            if (!passwordRegex.test(pasted)) {
                $(self).val(orig);
            }
        });
    });

    $(document).on('keypress', '.onlyPassword', function (e) {
        // backspace
        if (e.which == 8) return true;
        if (e.which == 13) return true;
        //if (e.which == 64) return true;//64:@
        if (e.keyCode === 9) return true;
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (passwordRegex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    var floatRegex = new RegExp('^[\-0-9.]*$');
    $(document).on('input', '.onlyDecimal4_1 input', function (event) {
        var oldValue = event.target.defaultValue;
        var newValue = $(this).val();

        var isValid = true;
        var array = newValue.split(".");
        var array1 = newValue.split("-");
        if (array.length === 2 && array[0].length === 0 || array.length > 2) isValid = false;
        if (array1.length === 2 && array1[0].length > 0 || array1.length > 2) isValid = false;
        if (array[0].length == 4) {
            var native = array[0];
            if (native[0] !== '-')  isValid = false;
        }
        if (array[0].length > 4) isValid = false;
        if (array.length > 1 && array[1].length > 1) isValid = false;

        var currentText = TextDiff(oldValue, newValue);
        if (!floatRegex.test(currentText) || !isValid) {
            $(this).val(oldValue);
        } else {
            event.target.defaultValue = newValue;
        }

    });
};

window.emailRegex = new RegExp('^[A-Za-z0-9._-]+@[a-zA-Z0-9.-]+.[A-Za-z]{2,3}$');
window.passwordRegex = '^([a-zA-Z0-9])+$';

function checkEmail(field, rules, i, options) {
    if (!field.val().match(window.emailRegex)) {
        return field.attr("data-errormessage-custom-error");
        //return options.allrules.validate2fields.alertText;
    }
}
function notEquals(field, rules, i, options){
    if(field.val() == $('#txtUserName').val()){
        return Lang.get('common.user_registration_password_username_equals');
    }
}

function checkPassword(field, rules, i, options) {
    if (!field.val().match(window.passwordRegex)) {
        return field.attr("data-errormessage-custom-error");
        //return options.allrules.validate2fields.alertText;
    }
}

function validateNumber(event) {
    if (event.altKey || event.ctrlKey || event.shiftKey || event.shiftKey
        || event.keyCode == 8 || event.keyCode == 116)
        return true;

    var keyCode = ('which' in event) ? event.which : event.keyCode;

    isNumeric = (keyCode >= 48 /* KeyboardEvent.DOM_VK_0 */ && keyCode <= 57 /* KeyboardEvent.DOM_VK_9 */)
        || (keyCode >= 96 /* KeyboardEvent.DOM_VK_NUMPAD0 */ && keyCode <= 105 /* KeyboardEvent.DOM_VK_NUMPAD9 */);

    if (!isNumeric) {
        event.stopPropagation();
        return false;
    }

    return true;
}

function closeFancy() {
    $.fancybox.close(true);


}
function closeFancyMap() {
    $.fancybox.close(true);
    if (typeof gisMap != "undefined") {
        window.location.href = window.base_url + '/reload';
    }
}

function closeMergingMap() {

    closeFancy();
    if (typeof gisMap != "undefined" && gisObject.returnMap) {
        window.location.href = window.base_url + '/reload';
    }
    return true;
}
/*
 * Control Tab Key with class
 * use id name
 */
function controlTab(firstId, lastId) {
    var firstId1 = '#' + firstId;
    var lastId1 = '#' + lastId;
    $(document).on('keydown', lastId1, function (e) {
        if (e.keyCode == 9 && !e.shiftKey) {
            $(firstId1).focus();
            e.preventDefault();
        }

    });
    $(document).on('keydown', firstId1, function (e) {
        if (e.keyCode == 9 && e.shiftKey) {
            $(lastId1).focus();
            e.preventDefault();
        }

    });
};
/*
 * Control Tab Key
 * use class name
 */
function controlTabC(firstClass, lastClass) {
    $('.' + lastClass).keydown(function (e) {
        if (e.keyCode == 9 && !e.shiftKey) {
            $('.' + firstClass).focus();
            e.preventDefault();
            return;
        }
    });
    $('.' + firstClass).keydown(function (e) {
        if (e.keyCode == 9 && e.shiftKey) {
            $('.' + lastClass).focus();
            e.preventDefault();
            return;
        }
    });
};

function openDialog(loadingUrl, width, height, dialogId) {
    if (dialogId === undefined) dialogId = "dialog1";
    dialogId = "dialog1";
    loadingUrl = window.base_url + loadingUrl;
    $.ajax({
        url: loadingUrl,
        success: function (data) {
            $('#' + dialogId).html(data);
            $("#" + dialogId).dialog({
                title: $("#" + dialogId + ' title').text() !== "" ? $("#" + dialogId + ' title').text() : "This is default title",
                width: width,
                height: height,
                modal: true
            });

        }

    });
    return;

    if (dialog === undefined) dialog = "dialog1";
    url = window.base_url + url;
    $("#" + dialog).dialog({
        //autoOpen: false,
        title: $("#" + dialog + ' title').text() !== "" ? $("#" + dialog + ' title').text() : "This is default title",
        width: width,
        height: height,
        modal: true,
        autoReposition: true,
        close: function () {
        }
    }).load(url);
};

function closeDialog() {
    $('#dialog1').dialog('close');
};

function resizeGrid(gridId, gridId2, gridId3, gridId4) {

    $(window).resize(
            function () {

            if (window.afterResize) {
                clearTimeout(window.afterResize);
            }

            window.afterResize = setTimeout(function () {
                $("#" + gridId).jqGrid('setGridWidth',
                    jQuery("#gbox_" + gridId).parent().width() - 2);
            }, 500);

            window.afterResize = setTimeout(function () {
                $("#" + gridId2).jqGrid('setGridWidth',
                    jQuery("#gbox_" + gridId2).parent().width() - 2);
            }, 500);

            window.afterResize = setTimeout(function () {
                $("#" + gridId3).jqGrid('setGridWidth',
                    jQuery("#gbox_" + gridId3).parent().width() - 2);
            }, 500);

            window.afterResize = setTimeout(function () {
                $("#" + gridId4).jqGrid('setGridWidth',
                    jQuery("#gbox_" + gridId4).parent().width() - 2);
            }, 500);
        });
};
function resizeGridEdit(gridId, gridId2, gridId3, gridId4){
    $("#" + gridId).jqGrid('setGridWidth',jQuery("#gbox_" + gridId).parent().width() - 2);
    $("#" + gridId2).jqGrid('setGridWidth',jQuery("#gbox_" + gridId2).parent().width() - 2);
    $("#" + gridId3).jqGrid('setGridWidth',jQuery("#gbox_" + gridId3).parent().width() - 2);
    $("#" + gridId4).jqGrid('setGridWidth',jQuery("#gbox_" + gridId4).parent().width() - 2);
}

function hideAutoComplete() {
    var display = $(".search_suggestion_holder").css("display");
    if (display === 'block' && !module.showing) {
        $(".search_suggestion_holder").css("display", "none");
        $(".editingcollor-button-group").css("display", "block");
    }
    module.showing = false;
};

