@extends('popup')
@section('content')

<div id="dialog1" class="dialog frm-validation-import"></div>
<div id="dialog2" class="dialog"></div>
<div id="dialog3" class="dialog"></div>
<style type="text/css">
.fancybox-wrap{
	width:95% !important;
}
.fancybox-inner {
        overflow-x: hidden  !important;
}
legend{
        border-bottom: none;
}
.ui-jqgrid-sortable{
    cursor: default !important;
}
.ui-state-default{
    color: #ffffff !important;
}
</style>
<div class="form-group1">
    <div class="col-md-12 creatingmap-title" >
    	<span style="color: white;font-size: 30px;">{{ trans('common.title_create_fertilizer_map') }}</span>

    </div>
    <div class="col-md-12 creatingmap-wapper">
    	<div class="col-md-8 creatingmap-subtitle">
            <span>{{trans('common.caption_create_fertilizer_map')}}</span>
    	</div>
        <a style="float: right" href="javascript:void(0)" onclick="getHelp(1);">{{ trans("common.help_label") }}</a>
    </div>
    <div id="data-input" class="col-md-12 creatingmap-wapper maptext" onclick="creatingmap.setEdited();">
        <div id="cover"></div>
<!--         {!! Form::open(array('route' => 'submit-creating-map','method' => 'post','class' => 'form-horizontal creating-map-frm')) !!} -->
        {!! Form::open(array('route' => 'map.confirm','method' => 'post','class' => 'form-horizontal creating-map-frm')) !!}


                <!-- Begin column 1 -->
        <div class="col-md-6" style="background-color: #FFFBD3;">
        	<div class="creatingmap-name">
            	<strong>{{trans('common.select_import_layer_map_to_destination_map_name')}}:</strong>
            </div>
            <div  class="date">
            	<span class="line-height">{{$map->folderLayer->name}}</span>
            </div>
            <div class="col-md-12  form-inline">
                <div class="crop-text"> <strong>{{ trans('common.create_fertilizer_map_label_crop') }}</strong></div>


                {!! Form::select('crops_id', $crops, $crop->id, array('id'=>'crops_id','class' => 'form-control crops_id col-md-5', 'onchange'=>'creatingmap.changeFertilizer();','autofocus')) !!}
                <input type="hidden" id="crop_name" name ="crop_name" />
                <input type="hidden" id="fertility_map_id" name ="fertility_map_id" value="{{$map->id}}" />
                <input type="hidden" id="listNitrogens" name ="listNitrogens" value="{{$listNitrogens}}" />
                <input type="hidden" id="user_id_main" name ="user_id_main" value="{{$user_id_main}}" />

                <input type="hidden" id="isCreate" name="isCreate" value=0 />
                {{--Thung 1--}}
                <input type="hidden" id="one_barrel_fertilizer_name" name ="one_barrel_fertilizer_name" />
                <input type="hidden" id="one_barrel_n" name ="one_barrel_n" />
                <input type="hidden" id="one_barrel_p" name ="one_barrel_p" />
                <input type="hidden" id="one_barrel_k" name ="one_barrel_k" />

                {{--Thung 2 - main--}}
                <input type="hidden" id="main_fertilizer_name" name ="main_fertilizer_name" />
                <input type="hidden" id="main_fertilizer_n" name ="main_fertilizer_n" />
                <input type="hidden" id="main_fertilizer_p" name ="main_fertilizer_p" />
                <input type="hidden" id="main_fertilizer_k" name ="main_fertilizer_k" />

                {{--Thung 2 - sub--}}
                <input type="hidden" id="sub_fertilizer_name" name ="sub_fertilizer_name" />
                <input type="hidden" id="sub_fertilizer_n" name ="sub_fertilizer_n" />
                <input type="hidden" id="sub_fertilizer_p" name ="sub_fertilizer_p" />
                <input type="hidden" id="sub_fertilizer_k" name ="sub_fertilizer_k" />

                <input type="hidden" id="fixed_fertilizer_amount" name ="fixed_fertilizer_amount" />
                <input type="hidden" id="fertilizer_price" name ="fertilizer_price" />
                <input type="hidden" id="fertilizer_price_sub" name ="fertilizer_price_sub" />
                <input type="hidden" id="fertilizer_price_type" name ="fertilizer_price_type" />
                <input type="hidden" id="fertilizer_price_sub_type" name ="fertilizer_price_sub_type" />
                <input type="hidden" id="soil_analysis_k_label" name ="soil_analysis_k_label" />
                <input type="hidden" id="soil_analysis_p_label" name ="soil_analysis_p_label" />   
                
                <input type="hidden" id="table3" name ="organic_matter_fields" />
                <input type="hidden" id="table4" name ="fertilization_stages" />
                <!-- Store polygonSelectionArea when creating a new fertilizer map -->
                <input id="polygonSelectionAreaForFertilizerCreationConfirmation" name="polygonSelectionAreaForFertilizerCreationConfirmation" value='' style="display:none;">
            </div>
            <fieldset class="col-md-12 panel panel-primary">
                <legend class="legend form-margin"><span class="required-icon">{{trans('common.title_create_fertilizer_map_required')}}</span><strong>{{ trans('common.create_fertilier_map_label_option_1') }}</strong></legend>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                              <input type="radio" name="fertilizing_machine_type" id="optionsRadios1 type_default" value="1" onchange="creatingmap.changeMachineType();" >{{ trans('common.create_fertilizer_map_label_radio_button_1') }}
                            </label><br>
                            <div id="machine_type_1">
                                <div class="legend-level2">

                                    <div class="table-name"><strong>{{ trans('common.create_fertilizer_map_label_table_1') }}</strong></div>
                                    <table id="jqGrid1" onclick="creatingmap.setEditing();"></table>
                                    <div class="table-option">{{ trans('common.create_fertilizer_map_label_option_table_1') }}</div>
                                </div>
                                <div class="col-md-11 col-md-offset-1">
                                    <div class="radio">
                                        <!-- lua chon 1.1 -->
                                        <label>
                                            <input type="radio" tabindex="201" id="control_methodology_11" name="control_methodology"  onchange="creatingmap.changeControlMethod();" value="1" >{{ trans('common.create_fertilizer_map_radio_button_1.1') }}
                                        </label><br>
                                        <!-- lua chon 1.2 -->
                                        <label>
                                            <input type="radio" tabindex="202" id="control_methodology_12" name="control_methodology"  onchange="creatingmap.changeControlMethod();" value="2">{{ trans('common.create_fertilizer_map_radio_button_1.2') }}
                                        </label><br>
                                        <!-- lua chon 1.3 -->
                                        <label>
                                            <input type="radio" id="control_methodology_13" name="control_methodology"  onchange="creatingmap.changeControlMethod();" value="3" >{{ trans('common.create_fertilizer_map_radio_button_1.3') }}
                                        </label><br>
                                        <!-- lua chon 1.4 -->
                                        <label>
                                            <input type="radio" id="control_methodology_14" name="control_methodology"  onchange="creatingmap.changeControlMethod();" value="4">{{ trans('common.create_fertilizer_map_radio_button_1.4') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <label>
                              <input type="radio" name="fertilizing_machine_type" id="optionsRadios2" onchange="creatingmap.changeMachineType();" value="2">{{ trans('common.create_fertilizer_map_label_radio_button_2') }}
                            </label>
                            <div id="machine_type_2">

                                <div class="legend-level2 relative">
                                    <div class="table-name">{{ trans('common.create_fertilizer_map_label_table_2') }}</div>
                                    <table id="jqGrid2" onclick="creatingmap.setEditing();"></table>
                                    <div class="table-option">{{ trans('common.create_fertilizer_map_label_option_table_2') }}</div>
                                </div>
                                <div class="radio col-md-11 col-md-offset-1">
                                    <!-- lua chon 2.1 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" id="control_methodology_21" name="control_methodology"   value="5" >{{ trans('common.create_fertilizer_map_radio_button_2.1') }}
                                    </label>
                                    <div class="form-group form-inline" style="margin-left: 0">
                                        <label id="exampleInputName2 ">{{ trans('common.create_fertilizer_map_radio_button_2.1.1') }}</label>
                                        <input type="text" id="fixed_fertilizer_amount5" class="form-control onlyDecimal6_2"style="width: 50px" ><span>{{ trans('common.create_fertilizer_map_radio_button_2.1.2') }}</span>
                                    </div>
                                    <!-- lua chon 2.2 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="6">{{ trans('common.create_fertilizer_map_radio_button_2.2') }}
                                    </label>
                                    <div class="form-group form-inline" style="margin-left: 0">
                                        <label for="exampleInputName2">{{ trans('common.create_fertilizer_map_radio_button_2.2.1') }}</label><input type="text" id="fixed_fertilizer_amount6" class="form-control onlyDecimal6_2" style="width: 50px" ><span>{{ trans('common.create_fertilizer_map_radio_button_2.1.2') }}</span>
                                    </div>
                                    <!-- lua chon 2.3 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="7" >{{ trans('common.create_fertilizer_map_radio_button_2.3') }}
                                    </label><br>
                                    <!-- lua chon 2.4 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="8" >{{ trans('common.create_fertilizer_map_radio_button_2.4') }}
                                    </label>

                                </div>

                            </div>
                        </div>

                    </div>
                </fieldset>

                <div class="col-md-12 form-inline general-group">
                    <div class="general-row mesh-size col-md-8">
                        <span><span class="required-icon">{{trans('common.title_create_fertilizer_map_required')}}</span><strong>{{ trans('common.creating_fertilizer_map_label_mesh_size') }}</strong></span>

                        {!! Form::text('mesh_size','', array('id' => 'mesh_size', 'maxlength'=>'2','style' =>'width: 56px ! important;','class' => 'form-control mesh_size onlyNumeric validate[required,min[10],max[50]] ',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_mesh_size_required"),
                            'data-errormessage-range-underflow' =>  trans("common.creatingmap_mesh_size_min"),
                            'data-errormessage-range-overflow' => trans("common.creatingmap_mesh_size_max")
                         )) !!}

                        <span> m</span>
                    </div>
                    <div class="general-row new1 col-md-6">
                        <span ><span class="required-icon">{{trans('common.title_create_fertilizer_map_required')}}</span><strong>{{ trans('common.creating_fertilizer_map_label_new_value1') }}</strong></span>

                        {!! Form::text('main_fertilizer_usual_amount','', array('id' => 'new1', 'maxlength'=>'3','style' =>'width: 56px;', 'class' => 'form-control onlyNumeric validate[required]',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_new_value1_required")
                         )) !!}
                        <span> kg/10a</span>
                    </div>
                    <div class="general-row new2 col-md-6">
                        <span ><strong>{{ trans('common.creating_fertilizer_map_label_new_value2') }}</strong></span>

                        {!! Form::text('sub_fertilizer_usual_amount','', array('id' => 'new2', 'maxlength'=>'3','style' =>'width: 56px;', 'class' => 'form-control onlyNumeric validate[required]',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_new_value2_required")
                         )) !!}
                         <span> kg/10a</span>
                    </div>
                </div>
        </div>
		<!-- End column 1 -->

		<!-- Begin column 2 -->
        <div class="col-md-6 " style="background-color: #FFFBD3">
            <fieldset class="panel panel-primary" style="width: 100%">
                <legend class="legend"><span class="required-icon">{{trans('common.title_create_fertilizer_map_required')}}</span><strong>{{ trans('common.create_fertilier_map_label_option_2') }}</strong></legend>
                <div class="panel-body" style="width: 100%">
                    <div class="form-inline">

                        {!! Form::select('fertilizer_standard_definition_id', $fertilizers, $initialId, array('id'=>'fertilizer_standard_definition_id', 'class' => 'form-control fertilizer_standard_definition_id validate[required]', 'onchange'=>'creatingmap.getFertilizers();',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_fertilizer_required")
                        )) !!}
                        <br><span>{{ trans('common.creating_fertilizer_map_range_used') }}<span id="fertilizer_range"></span></span><br>
                        <label >{{ trans('common.creating_fertilizer_map_notes') }}</label>
                    </div>
                </div>
                <div class="panel panel-default textarea">
                      <p id="fertilizer_notes" style="margin-left: 5px">

                      </p>
                </div>
          </fieldset>
            <fieldset class="panel panel-primary soil_analysis_value" style="display: block;">
                <legend class="legend"><span class="required-icon">{{trans('common.title_create_fertilizer_map_required')}}</span><strong>{{ trans('common.create_fertilier_map_label_option_3') }}</strong></legend>
                  <label style="display: block">
                    <input type="radio" name="soil_analysis_type" id="optionsRadios1 soil_analysis_radio1" value="1" onchange="creatingmap.changeAnalysisType();" style="margin-left: 5px">{{ trans('common.create_fertilizer_map_radio_button_3.1') }}
                  </label>
                  <label>
                    <input type="radio" name="soil_analysis_type" id="optionsRadios2" value="2" onchange="creatingmap.changeAnalysisType();" style="margin-left: 5px">{{ trans('common.create_fertilizer_map_radio_button_3.2') }}
                  </label>

                <div id="analysis_type_2" class="form-horizontal analysis_type_2 relative">
                    <div id="analysis_type_2_cover" style="display: block;"></div>
                    <label class="control-label" style="margin-left: 5px">{{trans('common.option_photpho')}} : </label>

                    {!! Form::select('p', $photphos, null, array('id'=>'p','class' => 'form-margin')) !!}
                    <br>
                    <label class="control-label" style="margin-left: 5px" >{{trans('common.option_kali')}} : </label>
                    {!! Form::select('k', $kalis, null, array('id'=>'k','class' => 'form-margin')) !!}
                </div>
            </fieldset>
            <div class="table-name"><strong>{{ trans('common.create_fertilizer_map_label_table_3') }}</strong></div>
            <table id="jqGrid3" onclick="creatingmap.setEditing();"></table>

          	<div class="row">
		        <div class="col-md-12">
		        	<div class="table-name"><strong>{{ trans('common.create_fertilizer_map_label_table_4') }}</strong></div>
		        </div>
    		</div>

		    <div class="row">
		        <div class="col-md-12">
		            <div class="table-fertilizer">
            			<table id="jqGrid4"></table>
                        <div id="jqGridPager4"></div>
                        <table id="rowed3"></table>
                        <div id="prowed3"></div>
                        <br>
		            </div>
		        </div>
		    </div>

	        <div id="submit-group" class="button-group ">
			    <span><button type="button" class="button-submit btn-save-fertilizer" onclick=" creatingmap.showConfirm();">{{trans('common.button_alert_ok')}}</button></span>
			     <span><button type="button" class="button-submit button-cancel-add-fertilizer" id="button-cancel-add-fertilizer" onclick="closeFancy();">{{trans('common.button_cancel')}}</button></span>
	        </div>
		</div>
		<!-- End column 2 -->
        {!! Form::close() !!}

    </div>
</div>
	<script src="{{url('/js/modules/load_creating_map_table.js')}}"></script>
	<script src="{{url('/js/modules/creatingmap.js')}}"></script>
    <script>
    loadGridTable.initPage();
    $('#jqGrid1').focus(function(){
        $('#control_methodology_11').focus();
        e.preventDefault();
    });
    $('#jqGrid2').focus(function(){
        $('#control_methodology_21').focus();
        e.preventDefault();
    });
    resizeGrid('jqGrid1','jqGrid2','jqGrid3','jqGrid4');
    creatingmap.changeOptions();
    creatingmap.getFertilizers();
    //Get polygon Selection area from main view and assign this value to hidden input in this popup
    $( document ).ready(function() {
        $("#polygonSelectionAreaForFertilizerCreationConfirmation").val($("#polygonSelectionAreaForFertilizerCreation").val());
        setTimeout(function(){
           $('.crops_id').focus();
            $("input:radio[id='optionsRadios1 type_default']").trigger('click');
        },0);
        controlTabC('mesh_size','crops_id');
        controlTabC('crops_id','button-cancel-add-fertilizer');
        controlTabC('btn-save-fertilizer','mark-tab-total');
        controlTabC('mark-tab-nito','fertilizer_standard_definition_id');
        $('input').on('click',function(event){
            setTimeout(function(){
                event.target.focus();
            },300);
        })
    });
    </script>
   @endsection



