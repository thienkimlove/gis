@extends('popup')
@section('content')
        {!! Form::model($infomation = new \StdClass, array(
               'url' => '/folders/fertilizer-map-payment',
               'method' => 'post',
               'class' => 'form-horizontal form-buy-fertilizer'
            )) !!}
         <input type="hidden" name="layer_id" value={{$layer->id}}>
         <input type="hidden" name="area" value={{$area}}>
         <input type="hidden" name="download_id" value={{$id}}>
        <div class="modal-header" style="min-height:55px;">
            <a style="float: right" href="javascript:void(0)" onclick="getHelp(2);">{{ trans("common.help_label") }}</a>
        </div>
        <div class="modal-body">
          <table class="table table-striped" style="font-size: 13px;">
            <tbody>
            <tr>
                <td colspan="2" class="title-table">{{trans('common.fertilizer_map_buy_header')}}</td>
            </tr>
            <tr class=" background-color1"><td class="col-sm-4" style="padding-left: 10px;">{{trans('common.fertilizer_map_id_label')}}</td>
                <td style="padding-left: 10px;">{{$id}}</td>
            </tr>
              <tr class=" background-color2"><td class="col-sm-4" style="padding-left: 10px;">{{trans('common.fertilizer_map_name_label')}}</td>
                <td style="padding-left: 10px;">{{$layer->name}}</td>
              </tr>
            </tr>
            <tr class=" background-color2"><td class="col-sm-4" style="padding-left: 10px;">{{trans('common.fertilizer_mesh_size_label')}}</td>
                <td style="padding-left: 10px;">{{$meshSize}}</td>
            </tr>
            <tr class=" background-color1"><td style="padding-left: 10px;">{{trans('common.crop_name_label')}}</td>
                <td style="padding-left: 10px;">{{$cropName}}</td>
            </tr>
              <tr class=" background-color2"><td style="padding-left: 10px;">{{trans('common.area_label')}}</td>
                <td style="padding-left: 10px;"> {{$area}}</td>
              </tr>
              <tr class=" background-color1"><td style="padding-left: 10px;">{{trans('common.unit_price_label')}}</td>
                <td style="padding-left: 10px;"> {{$unitPrice.trans('common.unit_price_value')}}</td>
              </tr>
              <tr class=" background-color2"><td style="padding-left: 10px;">{{trans('common.total_amount_label')}}</td>
                <td style="padding-left: 10px;">{{$total.trans('common.total_amount_value')}}</td>
              </tr>
            <tr class=" background-color1"><td style="padding-left: 10px;">{{trans('common.purchase_date_label')}}</td>
                <td style="padding-left: 10px;">{{$date->format(trans('common.date_format_rule'))}}</td>
            </tr>
            </tbody>
          </table>
            <p class="color-message">{{trans('common.message_download_screen_1')}}</p>
            <p class="color-message">{{trans('common.message_download_screen_2')}}</p>
             </div>

        <div class="modal-footer">
          <button id='download-button' class="button-submit btn-save-buy-fertilizer" type="button">{{trans('common.button_download')}}</button>
            <button class="button-submit btn-cancel-popup" type="button" >{{trans('common.button_cancel')}}</button>
        </div>
        {!!Form::close()!!}
@endsection

@section('footer')
<script src="{{url('/js/modules/buy_fertilizer.js')}}"></script>
@endsection