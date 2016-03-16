@extends('popup')

@section('content')
<style>
.fancybox-wrap{
	width:890px !important;
	top: 20px!important;
}
.fancybox-inner {
        overflow-x: hidden  !important;
}
.col1{
width:90px;
}
.col2{
width:155px;
}
.col3{
width:70px;
}
.col4{
width:90px;
}
.col5{
width:90px;
}
.col-md-7 {
  width: 56%;
}
.col-md-5 {
  width: 44%;
}
#legend_export{
  position: absolute;
  top: 170px;
  right: 16px;
  background-color:#fff;
}
h4{
  margin-bottom:0px;
  font-size: 14px;
}
.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th{
  border: 1px solid #524D4D;
}
.table>tbody>tr.info>th,.table>tbody>tr.info>td{
  background-color:#fff;
}
    .button-submit{
        margin: 0;
    }
</style>

<div class="modal-body">
<a style="float: right" id="helpLink" href="javascript:void(0)" onclick="getHelp(3);">{{ trans("common.help_label") }}</a>
    <div class="form-export-s1">

        <input type="hidden" id="namePrint" name="namePrint" value="{{$map['fertilizer_map_name']}}{{date("Y-m-d")}}" />
        {!! Form::open(array('name' => 's1-export-frm','class' => 'form-horizontal frm-validation-s1-export')) !!}

        <h2 class="page-header">{{ trans('common.export_pdf_title') }}</h2>
        <div class="form-group">
        <label class="col-md-4">{{trans('common.label_map_title')}}</label>
        <div class="col-md-8">
            {!! Form::text('map_title', $map['fertilizer_map_name'], [
               'id' => 'export_pdf_title',
               'class' => 'form-control export_pdf_title input-md validate[required,maxSize[100]]',
               'maxLength'=>'100',
               'data-errormessage-value-missing' =>  trans("common.label_map_title_required"),
               'data-prompt-position'=>"topRight:-100"
            ]) !!}
        </div>
        </div>
        <div class="form-group">
        <label class="col-md-4">{{trans('common.label_legend_title')}}</label>
        <div class="col-md-8">
            {!! Form::checkbox('legend', 1, true, [
               'id' => 'export_pdf_legend',
               'class' => 'form-control'
            ]) !!}
        </div>
        </div>
        <div class="form-group">
        <label class="col-md-4">{{trans('common.label_scale_title')}}</label>
        <div class="col-md-8">
            {!! Form::checkbox('scale_bar', 1, true, [
              'id' => 'export_pdf_scale_bar',
              'class' => 'form-control'
            ]) !!}
        </div>
        </div>
        <div class="form-group">
        <label class="col-md-4">{{trans('common.label_user_input')}}</label>
        <div class="col-md-8">
            {!! Form::textarea('text', null, [
              'id' => 'export_pdf_free_text',
              'class' => 'form-control',
              'style'=>'height:100px',
              'maxLength'=>'81'

            ]) !!}
        </div>
        </div>
        <input id="export_pdf_map_infos" type="hidden" value="{{json_encode($map)}}" />
        <input type="hidden" id="export_pdf_fertilizer_id" value="{{$map['fertilizer_map_id']}}" />
        <hr>
            {!! Form::button(
                    trans('common.confirm_ok'),
                    array(
                        'class' => 'button-submit ok-export-pdf',
                        'type' => 'button'
                    )
                )
            !!}
            {!! Form::button(
                    trans('common.button_cancel'),
                    array(
                        'id' => 'cancel-export-pdf',
                        'class' => 'button-submit cancel-export-pdf',
                        'type' => 'button',
                        'style'=>'margin:8px 0 0 0;',
                        'onlick'=>'parent.$.fancybox.close();'
                    )
                )
            !!}
        {!! Form::close() !!}
        </div>
    </div>
    <div class="form-export-s2 col-xs-12" id="pdf_content" style="display: none;background-color: #ffffff;font-size: 13px;">
        <div id="PDFTitle">
        <h4><span id="mapTitle"></span><span style="float: right;"> <?php echo date("Y/m/d H:i")?></span></h4>
        <span></span>
        </div>
        <div id="map_export" class="map" style="border:1px solid"></div>
        <div id="legend_export" ><img src="" /></div>
        <div class="row">
        <div class="col-md-7">
        @if($data->fertilizerMapProperty->fertilizing_machine_type==1)
            <table class="table table-bordered" id="table_1">
              <tbody>
                <tr class="info table-bordered">
                  <th class="col1">{{trans('common.label_one_barrel')}}</th>
                  <td class="col2" style="text-align: left!important;">{{$data->fertilizerMapProperty->one_barrel_fertilizer_name}}</td>
                  <td class="col3">{{$data->main_sum}}{{trans('common.label_unit_kg')}}</td>
                  <td class="col4">{{$data->main_price}}{{trans('common.label_unit_yen')}}</td>
                  <td class="col5">{{$area}}{{trans('common.label_unit_ha')}}</td>
                </tr>
              </tbody>
            </table>
        @else
            <table class="table table-bordered" id="table_2">
              <tbody>
                <tr class="info table-bordered">
                  <th class="col1">{{trans('common.fertilizer_table2_Label1_val1')}}</th>
                  <td class="col2" style="text-align: left!important;">{{$data->fertilizerMapProperty->main_fertilizer_name}}</td>
                  <td class="col3">{{$data->main_sum}}{{trans('common.label_unit_kg')}}</td>
                  <td class="col4">{{$data->main_price}}{{trans('common.label_unit_yen')}}</td>
                  <td rowspan="2" style="vertical-align: middle;width:90px;">{{$data->main_sub_price}}{{trans('common.label_unit_yen')}}</td>
                  <td rowspan="2" style="vertical-align: middle;width:90px;">{{$area}}{{trans('common.label_unit_ha')}}</td>
                </tr>
                <tr class="info">
                  <th class="col1">{{trans('common.fertilizer_table2_Label1_val2')}}</th>
                  <td class="col2" style="text-align: left!important;">{{$data->fertilizerMapProperty->sub_fertilizer_name}}</td>
                  <td class="col3">{{$data->sub_sum}}{{trans('common.label_unit_kg')}}</td>
                  <td class="col4">{{$data->sub_price}}{{trans('common.label_unit_yen')}}</td>
                </tr>
                </tbody>
            </table>
        @endif
        </div>
        <div class="col-md-5">
        <p id="free-text"></p>

        </div>
        <div class="col-md-12">
        <p style="float: right;padding-right: 10px;">{{trans('common.label_copy_right')}}{{$systemInfo->content}} {{$systemInfo->version}}</p>
        </div>
        </div>
        </div>
    <div class="form-group">
        <div id="button-2" style="display: none;margin-left:15px;margin-bottom: 15px; ">
        <button id="generate-pdf" class="generate-pdf button-submit" >{{trans('common.button_download_export')}}</button>
        <button id="cancel-generate-pdf"  class="cancel-generate-pdf button-submit" >{{trans('common.button_cancel')}}</button>
        </div>
    </div>


<script src="{{url('js/libs/jsPDF_lib/jspdf.debug.js')}}" type="text/javascript"></script>
<script src="{{url('js/modules/export_pdf.js')}}" type="text/javascript"></script>
    <script>
        setTimeout(function(){
           $('.export_pdf_title').focus();
        },0);
        controlTabC('export_pdf_title','cancel-export-pdf');
        $('.ok-export-pdf').click(function(){
            setTimeout(function(){
                $('.generate-pdf').focus();
            },0);
        });
        controlTabC('generate-pdf','cancel-generate-pdf');
    </script>
@endsection
