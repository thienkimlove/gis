<style>
    .title{
        font-weight:bold;
    }
    .text-style{
        margin-left: 20px;
    }
    .modal-body{
        line-height: 1.7;
    }
</style>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel">{{trans('common.fertilizer_map_confirm_header')}}</h3>
      </div>
      <div class="modal-body ">
        <span class="title">{{trans('common.crop_name_label')}}</span><span>{{$postData['crop_name']}}</span><br>
        <span class="title">{{trans('common.fertilizer_table_name')}}</span><br>
            <div class="col-sm-10">
            @if($postData['fertilizing_machine_type']==1)
                <table class="table " id="table_1">
                  <thead>
                    <tr class="success">
                      <th>{{trans('common.fertilizer_table_label1')}}</th>
                      <th>{{trans('common.fertilizer_table_label2')}}</th>
                      <th>{{trans('common.fertilizer_table_label3')}}</th>
                      <th>{{trans('common.fertilizer_table_label4')}}</th>
                      <th>{{trans('common.fertilizer_table_label5')}}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="info">
                      <th scope="row">{{$postData['one_barrel_fertilizer_name']}}</th>
                      <td>{{$postData['one_barrel_n']}}</td>
                      <td>{{$postData['one_barrel_p']}}</td>
                      <td>{{$postData['one_barrel_k']}}</td>
                      <td>{{$postData['fertilizer_price']}}</td>
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
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="info">
                      <th>{{trans('common.fertilizer_table2_Label1_val1')}}</th>
                      <td>{{$postData['main_fertilizer_name']}}</td>
                      <td>{{$postData['main_fertilizer_n']}}</td>
                      <td>{{$postData['main_fertilizer_p']}}</td>
                      <td>{{$postData['main_fertilizer_k']}}</td>
                      <td>{{$postData['fertilizer_price']}}</td>
                    </tr>
                    <tr class="info">
                      <th>{{trans('common.fertilizer_table2_Label1_val2')}}</th>
                      <td>{{$postData['sub_fertilizer_name']}}</td>
                      <td>{{$postData['sub_fertilizer_n']}}</td>
                      <td>{{$postData['sub_fertilizer_p']}}</td>
                      <td>{{$postData['sub_fertilizer_k']}}</td>
                      <td>{{$postData['fertilizer_price_sub']}}</td>
                    </tr>
                  </tbody>
                </table>
            @endif
            </div>
        <div class="row">
            <div class="col-sm-6">
                <span class="title">{{trans('common.fertilizer_properties_control_method_label')}}</span>
                <?php
                 switch($postData['control_methodology']){
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
                <span class="title">{{trans('common.fertilizer_properties_label4')}}</span><br><span class="text-style">{{$fertility->folderLayer->name}}</span><br>
                <span class="title">{{trans('common.fertilizer_properties_label5')}}</span><span>{{$postData['mesh_size']}} m</span><br>
                <span class="title">{{trans('common.fertilizer_properties_label6')}}</span><br>
                <span class="text-style">{{$fertilizerStandardCrop->fertilization_standard_name}}</span><br>
                <span class="text-style">{{trans('common.fertilizer_properties_label7')}}</span><span>{{$fertilizerStandardCrop->range_of_application}}</span><br>
                <span class="text-style">{{trans('common.fertilizer_properties_label8')}}</span><span>{{$fertilizerStandardCrop->notes}}</span><br>
                <span class="title">{{trans('common.fertilizer_properties_label9')}}</span>
                @if($postData['soil_analysis_type']==1)
                {{trans('common.fertilizer_soil_analysis_none')}}
                @else
                <br>
                <span class="text-style">{{trans('common.fertilizer_properties_label10')}} {{$postData['soil_analysis_p_label']}}</span><br>
                <span class="text-style">{{trans('common.fertilizer_properties_label11')}}{{$postData['soil_analysis_k_label']}}</span><br>
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
                      <?php
                            $table3s[0]['type']=trans('common.organic_matter_type1');
                            $table3s[1]['type']=trans('common.organic_matter_type2');
                            $table3s[2]['type']=trans('common.organic_matter_type3');
                            $table3s[3]['type']=trans('common.organic_matter_type4');
                            $table3s[4]['type']=trans('common.fertilizer_table_sum');
                      ?>
                      @foreach($table3s as $value)
                        <tr class="info">
                          <th>{{$value['type']}}</th>
                          <td>{{$value['n']}}</td>
                          <td>{{$value['p']}}</td>
                          <td>{{$value['k']}}</td>
                        </tr>
                      @endforeach
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
                  <?php  $n=0;$p=0;$k=0;?>
                  @foreach($table4s as $value)
                    <?php
                        $n+=$value['n'];
                        $p+=$value['p'];
                        $k+=$value['k'];
                    ?>
                    <tr class="info">
                      <th>{{$value['fertilization_stage']}}</th>
                      <td>{{$value['n']}}</td>
                      <td>{{$value['p']}}</td>
                      <td>{{$value['k']}}</td>
                    </tr>
                  @endforeach
                    <tr class="info">
                      <th>{{trans('common.fertilizer_table3_sum')}}</th>
                      <td>{{$n}}</td>
                      <td>{{$p}}</td>
                      <td>{{$k}}</td>
                    </tr>
                  </tbody>
                </table>
            </div>
        </div>
      </div>
      <div class="modal-footer">

        @if(!empty($postData['is_recreate_fertilizer_map']) && $postData['is_recreate_fertilizer_map']==1)
        <button type="button" class="button-submit" onclick="$('#isCreate').val('0');
        creatingmap.submitCreatingMap();">{{trans('common.button_save_overWrite')}}</button>
        <button type="button" class="button-submit" onclick="$('#isCreate').val('1');
        creatingmap.submitCreatingMap();">{{trans('common.button_create_new')}}</button>
        @else
        <button type="button" class="button-submit" onclick="$('#isCreate').val('1');
        creatingmap.submitCreatingMap();">{{trans('common.button_save')}}</button>
        @endif
        <button type="button" class="button-submit" data-dismiss="modal" onclick="creatingmap.hideConfirm();">
        {{trans('common.button_cancel')}}</button>
      </div>
    </div>
</div>


