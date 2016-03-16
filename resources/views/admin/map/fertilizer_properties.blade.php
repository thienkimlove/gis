@extends('popup')
@section('content')
<style>
.fancybox-wrap{
	width:65% !important;
}
.fancybox-inner {
    overflow-x: hidden  !important;
}
.modal-body{
    line-height:1.7;
}
    .title{
        font-weight:bold;
    }
    .text-style{
        margin-left: 20px;
    }
</style>
  <h2 style="padding-left: 15px;">{{trans('common.fertilizer_map_properties_header')}}</h2>
  <hr style="margin-top:0;margin-bottom:0">
  <div class="modal-body ">
    <span class="title">{{trans('common.crop_name_label')}}</span><span>{{$data->crops->crops_name}}</span><br>
    <span class="title">{{trans('common.fertilizer_table_name')}}</span><br>
        <div >
        @if($data->fertilizerMapProperty->fertilizing_machine_type==1)
            <table class="table " id="table_1">
              <thead>
                <tr class="success">
                  <th>{{trans('common.fertilizer_table_label1')}}</th>
                  <th>{{trans('common.fertilizer_table_label2')}}</th>
                  <th>{{trans('common.fertilizer_table_label3')}}</th>
                  <th>{{trans('common.fertilizer_table_label4')}}</th>
                  <th>{{trans('common.fertilizer_table_label5')}}</th>
                  <th>{{trans('common.fertilizer_table_label6')}}</th>
                  <th>{{trans('common.fertilizer_table_label7')}}</th>
                </tr>
              </thead>
              <tbody>
                <tr class="info">
                  <th scope="row">{{$data->fertilizerMapProperty->one_barrel_fertilizer_name}}</th>
                  <td>{{$data->fertilizerMapProperty->one_barrel_n}}</td>
                  <td>{{$data->fertilizerMapProperty->one_barrel_p}}</td>
                  <td>{{$data->fertilizerMapProperty->one_barrel_k}}</td>
                  <td>{{$data->fertilizerMapProperty->fertilizer_price}}</td>
                  <td>{{$data->main_sum}}</td>
                  <td>{{$data->main_price}}</td>
              </tbody>
            </table>
        @else
            <table class="table " id="table_2">
              <thead>
                <tr class="success">
                  <th>{{trans('common.fertilizer_table2_label1')}}</th>
                  <th>{{trans('common.fertilizer_table_label1')}}</th>
                  <th>{{trans('common.fertilizer_table_label2')}}</th>
                  <th>{{trans('common.fertilizer_table_label3')}}</th>
                  <th>{{trans('common.fertilizer_table_label4')}}</th>
                  <th>{{trans('common.fertilizer_table_label5')}}</th>
                  <th>{{trans('common.fertilizer_table_label6')}}</th>
                  <th>{{trans('common.fertilizer_table_label7')}}</th>
                </tr>
              </thead>
              <tbody>
                <tr class="info">
                  <th>{{trans('common.fertilizer_table2_Label1_val1')}}</th>
                  <td style="text-align: left!important;">{{$data->fertilizerMapProperty->main_fertilizer_name}}</td>
                  <td>{{$data->fertilizerMapProperty->main_fertilizer_n}}</td>
                  <td>{{$data->fertilizerMapProperty->main_fertilizer_p}}</td>
                  <td>{{$data->fertilizerMapProperty->main_fertilizer_k}}</td>
                  <td>{{$data->fertilizerMapProperty->fertilizer_price}}</td>
                  <td>{{$data->main_sum}}</td>
                  <td>{{$data->main_price}}</td>
                </tr>
                <tr class="info">
                  <th>{{trans('common.fertilizer_table2_Label1_val2')}}</th>
                  <td style="text-align: left!important;">{{$data->fertilizerMapProperty->sub_fertilizer_name}}</td>
                  <td>{{$data->fertilizerMapProperty->sub_fertilizer_n}}</td>
                  <td>{{$data->fertilizerMapProperty->sub_fertilizer_p}}</td>
                  <td>{{$data->fertilizerMapProperty->sub_fertilizer_k}}</td>
                  <td>{{$data->fertilizerMapProperty->fertilizer_price_sub}}</td>
                  <td>{{$data->sub_sum}}</td>
                  <td>{{$data->sub_price}}</td>
                </tr>
                <tr class="info">
                  <th></th>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>{{trans('common.fertilizer_table_sum')}}</td>
                  <td>{{$data->main_sub_sum}}</td>
                  <td>{{$data->main_sub_price}}</td>
                </tr>
              </tbody>
            </table>
        @endif
        </div>
    <div class="row">
        <div class="col-sm-6">
            <span class="title">{{trans('common.fertilizer_properties_control_method_label')}}</span>
            <?php
             switch($data->fertilizerMapProperty->control_methodology){
              case(1): {echo(trans('common.create_fertilizer_map_radio_button_1.1'));break;}
              case(2): {echo(trans('common.create_fertilizer_map_radio_button_1.2'));break;}
              case(3): {echo(trans('common.create_fertilizer_map_radio_button_1.3'));break;}
              case(4): {echo(trans('common.create_fertilizer_map_radio_button_1.4'));break;}
              case(5): {echo(trans('common.create_fertilizer_map_radio_button_2.1'));break;}
              case(6): {echo(trans('common.create_fertilizer_map_radio_button_2.2'));break;}
              case(7): {echo(trans('common.create_fertilizer_map_radio_button_2.3'));break;}
              case(8): {echo(trans('common.create_fertilizer_map_radio_button_2.4'));break;}
             }
            ?><br>
            <span class="title">{{trans('common.fertilizer_properties_label4')}}</span><br><span class="text-style">{{$data->fertilizerMap->fertilityMap->folderLayer->name}}</span><br>
            <span class="title">{{trans('common.fertilizer_properties_label5')}}</span><span>{{$data->fertilizerMapProperty->mesh_size}} m</span><br>
            <span class="title">{{trans('common.fertilizer_properties_label6')}}</span><br>
            <span class="text-style">{{$data->fertilizerStandardDefinition->fertilization_standard_name}}</span><br>
            <span class="text-style">{{trans('common.fertilizer_properties_label7')}}</span><span>{{$data->fertilizerStandardDefinition->range_of_application}}</span><br>
            <span class="text-style">{{trans('common.fertilizer_properties_label8')}}</span><span>{{$data->fertilizerStandardDefinition->notes}}</span><br>
            <span class="title">{{trans('common.fertilizer_properties_label9')}}</span>
            @if($data->fertilizerMapProperty->soil_analysis_type==1)
            {{trans('common.fertilizer_soil_analysis_none')}}
            @else
            <br>
            <span class="text-style">{{trans('common.fertilizer_properties_label10')}} {{$photphos[$data->fertilizerMapProperty->p]}}</span><br>
            <span class="text-style">{{trans('common.fertilizer_properties_label11')}}{{$kalis[$data->fertilizerMapProperty->k]}}</span><br>
            @endif
        </div>
        <div class="col-sm-6">
        <span class="title">{{trans('common.fertilizer_properties_label12')}}</span><br>
            <table class="table " id="table_3">
                  <thead>
                    <tr class="success">
                      <th>{{trans('common.fertilizer_table3_header1')}}</th>
                      <th>{{trans('common.fertilizer_table3_header2')}}</th>
                      <th>{{trans('common.fertilizer_table3_header3')}}</th>
                      <th>{{trans('common.fertilizer_table3_header4')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($data->fertilizerMap->organicMatterField as $value)
                    <tr class="info">
                      <th>{{$value->organic_matter_field_type}}</th>
                      <td>{{$value->n}}</td>
                      <td>{{$value->p}}</td>
                      <td>{{$value->k}}</td>
                    </tr>
                  @endforeach
                      <tr class="info">
                        <th>{{$data->fertilizerMap->organicMatterField->sum}}</th>
                        <td>{{$data->fertilizerMap->organicMatterField->n_sum}}</td>
                        <td>{{$data->fertilizerMap->organicMatterField->p_sum}}</td>
                        <td>{{$data->fertilizerMap->organicMatterField->k_sum}}</td>
                      </tr>
                  </tbody>
                </table>
        <span class="title">{{trans('common.fertilizer_properties_label13')}}</span><br>
            <table class="table " id="table_4">
              <thead>
                <tr class="success">
                  <th>{{trans('common.fertilizer_table4_header1')}}</th>
                  <th>{{trans('common.fertilizer_table3_header2')}}</th>
                  <th>{{trans('common.fertilizer_table3_header3')}}</th>
                  <th>{{trans('common.fertilizer_table3_header4')}}</th>
                </tr>
              </thead>
              <tbody>
              @foreach($data->fertilizerMap->fertilizerStage as $value)
                <tr class="info">
                  <th>{{$value->fertilization_stage}}</th>
                  <td>{{$value->n}}</td>
                  <td>{{$value->p}}</td>
                  <td>{{$value->k}}</td>
                </tr>
              @endforeach
                 <tr class="info">
                    <th>{{$data->fertilizerMap->fertilizerStage->sum}}</th>
                    <td>{{$data->fertilizerMap->fertilizerStage->n_sum}}</td>
                    <td>{{$data->fertilizerMap->fertilizerStage->p_sum}}</td>
                    <td>{{$data->fertilizerMap->fertilizerStage->k_sum}}</td>
                  </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
@endsection
