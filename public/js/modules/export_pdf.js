
//working with export pdf popup S1.

$(document).on('click', '.ok-export-pdf', function (){

    var exportConfig = {
        'title' : $('#export_pdf_title').val(),
        'legend' : $('#export_pdf_legend').is(':checked'),
        'scale_bar' : $('#export_pdf_scale_bar').is(':checked'),
        'free_text' : $('#export_pdf_free_text').val(),
        'fertilizer_id' : $('#export_pdf_fertilizer_id').val(),
        'map_infos' : $.parseJSON($('#export_pdf_map_infos').val())
    };

    if (!$('#export_pdf_title').validationEngine('validate')) {
        setTimeout(function(){
            $('#export_pdf_title').validationEngine('hideAll');
        }, 1000);
        return;
    }

    $('.form-export-s1').hide();
    $('.form-export-s2').show();
    $('#button-2').show();
    $('#helpLink').hide();

    gisMap.loadOutside(exportConfig);
    if($('#export_pdf_scale_bar').is(':checked')) {
        gisMap.map.addControl(new ol.control.ScaleLine({}));
    }
    $("#mapTitle").empty();
    $("#free-text").empty();
    $("#mapTitle").append($("#export_pdf_title").val().replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return '&#'+i.charCodeAt(0)+';';
    }));
    $("#free-text").append($("#export_pdf_free_text").val().replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return '&#'+i.charCodeAt(0)+';';
    }));
});
$(document).on('click', '#cancel-export-pdf', function () {
    $.fancybox.close();
});
$(document).on('click', '#generate-pdf', function () {
    //function convertImgToBase64(url, callback, outputFormat){
    //    var img = new Image();
    //    img.crossOrigin = 'Anonymous';
    //    img.onload = function(){
    //        var canvas = document.createElement('CANVAS');
    //        var ctx = canvas.getContext('2d');
    //        canvas.height = this.height;
    //        canvas.width = this.width;
    //        ctx.fillStyle = "#FFFFFF";
    //        ctx.fillRect(0,0,canvas.width,canvas.height);
    //        ctx.drawImage(this,0,0);
    //        var dataURL = canvas.toDataURL(outputFormat || 'image/png');
    //        callback(dataURL);
    //        canvas = null;
    //    };
    //    img.src = url;
    //}
    //var options = { background: '#fff'};
    //convertImgToBase64($('#legend_export >img').attr('src'), function(base64Img){
        html2canvas($("#pdf_content"), {
            onrendered: function(canvas) {
                var imgData = canvas.toDataURL(
                    'image/png');
                var doc = new jsPDF('l', 'pt','a4');
                doc.addImage(imgData, 'JPEG', 10, 50,820,  510,'SLOW');
                //if($('#export_pdf_legend').prop('checked')==true)
                //    doc.addImage(base64Img, 'PNG', 715, 170);
                doc.save($('#namePrint').val().concat('.pdf'));
            }
        });
    //});

});
$(document).on('click', '#cancel-generate-pdf', function () {
    $('.form-export-s1').show();
    $('.form-export-s2').hide();
    $('#button-2').hide();
    $('#helpLink').show();
});
//after that we go to draw in loadMap and process after draw in gisMap too.

//javascript code for creating map process located in map/map.blade.php
