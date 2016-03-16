$(function(){
    $("#start-date").datepicker({
        closeText: '閉じる',
        prevText: '&#x3C;前',
        nextText: '次&#x3E;',
        currentText: '今日',
        monthNames: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
        dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
        dayNamesShort: ['日','月','火','水','木','金','土'],
        dayNamesMin: ['日','月','火','水','木','金','土'],
        weekHeader: '週',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: '年',
        dateFormat: "yy-mm-dd"
    });

    $("#end-date").datepicker({
        closeText: '閉じる',
        prevText: '&#x3C;前',
        nextText: '次&#x3E;',
        currentText: '今日',
        monthNames: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
        dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
        dayNamesShort: ['日','月','火','水','木','金','土'],
        dayNamesMin: ['日','月','火','水','木','金','土'],
        weekHeader: '週',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: '年',
        dateFormat: "yy-mm-dd"
    });

    $('.btn-save-price').click(function(event){
        event.preventDefault();
        var titleMsg = Lang.get('common.error_title');
        gisForm.clickSave(event, {
            formEle : $('.frm-validation-price'),
            callbackFunction : function(data){
                if (data.code == 200) {
                    $.fancybox.close();
                    gisGrid.refresh();
                } else {
                    if(data)
                        fancyAlert(data.message, titleMsg);
                }
            }
        });
    });

    $('.btn-cancel-popup').click(function(event){
        event.preventDefault();
        $.fancybox.close(true);
    });
});