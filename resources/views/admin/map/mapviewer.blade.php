@extends('popup')
@section('content')

    <div id="dialog1" class="dialog frm-validation-import"></div>
    <div id="dialog2" class="dialog"></div>
    <div id="dialog3" class="dialog"></div>
    <style type="text/css">
        .fancybox-wrap{
            width:95% !important
        }
    </style>
    <style type="text/css">
        .fancybox-inner {
            overflow-x: hidden  !important;
        }
        .fancybox-inner #wrapper{
            /*width: 1000px;*/
        }
        legend{
            border-bottom: none;
        }
    </style>
    <div class="form-group1">
        <div class="col-md-12 creatingmap-title" >
            <span>Tạo sơ đồ bón phân khả biến - màn hình chỉ định điều kiện tạo</span>

        </div>
        <div class="col-md-12 creatingmap-wapper">
            <div class="col-md-8 creatingmap-subtitle">
                <span>{{trans('common.lbl_import_layer_map_to_destination_layer_map')}}</span>
            </div>
            <div class="col-md-4 creatingmap-wapper creatingmap-help">
                <a  href="">help</a>
            </div>
        </div>
        <div id="data-input" class="col-md-12 creatingmap-wapper maptext" onclick="creatingmap.setEdited();">
            <div id="cover"></div>
            <!--         {!! Form::open(array('route' => 'submit-creating-map','method' => 'post','class' => 'form-horizontal creating-map-frm')) !!} -->
            {!! Form::open(array('route' => 'map.confirm','method' => 'post','class' => 'form-horizontal creating-map-frm')) !!}


                    <!-- Begin column 1 -->
            <div class="col-md-6" style="background-color: #FFFBD3;">
                <div class="creatingmap-name">
                    <span>{{trans('common.select_import_layer_map_to_destination_map_name')}}:</span>
                </div>
                <div  class="date">
                    <span>map->folderLayer->name</span>
                </div>
                <div class="col-md-12  form-inline">
                    <div class="crop-text">Crop</div>


                    {!! Form::select('crops_id', $crops, $crop->id, array('id'=>'crops_id','class' => 'form-control col-md-5', 'onchange'=>'creatingmap.changeFertilizer();')) !!}
                    <input type="hidden" id="crop_name" name ="crop_name" />


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

                    <input type="hidden" id="table3" name ="organic_matter_fields" />
                    <input type="hidden" id="table4" name ="fertilization_stages" />
                </div>
                <fieldset class="col-md-12 panel panel-primary">
                    <legend class="legend form-margin">Panel with panel-primary class</legend>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                                <input type="radio" name="fertilizing_machine_type" id="optionsRadios1" value="1" onchange="creatingmap.changeMachineType();" checked>
                                Option one is this and that&mdash;be sure to include why it's great
                            </label>
                            <div id="machine_type_1">
                                <div class="legend-level2">

                                    <div class="table-name">This is table 1</div>
                                    <table id="jqGrid1" onclick="creatingmap.setEditing();"></table>
                                    <div class="table-option">Tuy chon cua bang 1:</div>
                                </div>
                                <div class="col-md-11 col-md-offset-1">
                                    <div class="radio">
                                        <!-- lua chon 1.1 -->
                                        <label>
                                            <input type="radio" name="control_methodology"  value="1" >
                                            Option one is this and that&mdash;be sure to include why it's great
                                        </label>
                                        <!-- lua chon 1.2 -->
                                        <label>
                                            <input type="radio" name="control_methodology"  value="2">
                                            Option two can be something else and selecting it will deselect option one
                                        </label>
                                        <!-- lua chon 1.3 -->
                                        <label>
                                            <input type="radio" name="control_methodology"  value="3" >
                                            Option one is this and that&mdash;be sure to include why it's great
                                        </label>

                                        <!-- lua chon 1.4 -->
                                        <label>
                                            <input type="radio" name="control_methodology"  value="4">
                                            Option two can be something else and selecting it will deselect option one
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <label>
                                <input type="radio" name="fertilizing_machine_type" id="optionsRadios2" onchange="creatingmap.changeMachineType();" value="2">
                                Option two can be something else and selecting it will deselect option one
                            </label>
                            <div id="machine_type_2">

                                <div class="legend-level2 relative">
                                    <div class="machine_type_2_cover" style="display: block;"></div>
                                    <div class="table-name">This is table 2</div>
                                    <table id="jqGrid2" onclick="creatingmap.setEditing();"></table>
                                    <div class="table-option">Tuy chon cua bang 2:</div>
                                </div>
                                <div class="radio col-md-11 col-md-offset-1">

                                    <div class="machine_type_2_cover" style="display: block;"></div>
                                    <!-- lua chon 2.1 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="1" >
                                        Option one is this and that&mdash;be sure to include why it's great
                                    </label>
                                    <div class="form-group form-inline">
                                        <label id="exampleInputName2 ">Nhap vao tuy chon: </label>
                                        <input type="text" id="fixed_fertilizer_amount1" class="form-control onlyDecimal6_2"style="width: 50px" ><span> kg/10a</span>
                                    </div>
                                    <!-- lua chon 2.2 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="2">
                                        Option two can be something else and selecting it will deselect option one
                                    </label>
                                    <div class="form-group form-inline">
                                        <label for="exampleInputName2">Nhap vao tuy chon: </label>
                                        <input type="text" id="fixed_fertilizer_amount2" class="form-control onlyDecimal6_2" style="width: 50px" ><span> kg/10a</span>
                                    </div>
                                    <!-- lua chon 2.3 -->
                                    <label>
                                        <input onchange="creatingmap.changeControlMethodology();" type="radio" name="control_methodology"  value="3" >
                                        Option one is this and that&mdash;be sure to include why it's great
                                    </label>
                                    <div class="form-group form-inline">

                                        <label class="control-label" style="margin-left: 5px">What's up : </label>
                                        <select class="form-margin" name="npk_type" id ="npk_type">
                                            <option value="">{{trans('common.select_item_null')}}</option>
                                            <option value="1">P</option>
                                            <option value="2">K</option>
                                        </select>

                                        <label for="exampleInputName2">Option: </label>
                                        <input type="text" id="fixed_fertilizer_amount3" class="form-control onlyDecimal6_2" style="width: 50px" ><span> kg/10a</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </fieldset>

                <div class="col-md-12 form-inline general-group">
                    <div class="general-row mesh-size col-md-4">
                        <span >Mesh size: </span>

                        {!! Form::text('mesh_size','', array('id' => 'fertilization_standard_name', 'maxlength'=>'2', 'class' => 'form-control mesh_size onlyNumeric validate[required,min[10],max[42]] ',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_fertilizer_required"),
                            'data-errormessage-range-underflow' =>  trans("common.creatingmap_mesh_size_min"),
                            'data-errormessage-range-overflow' => trans("common.creatingmap_mesh_size_max")
                         )) !!}

                        <span> m</span>
                    </div>
                    <div class="general-row new1 col-md-4">
                        <span >New value 1: </span>

                        {!! Form::text('new1','', array('id' => 'new1', 'maxlength'=>'3','style' =>'width: 80px', 'class' => 'form-control onlyNumeric validate[required]',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_new_value1_required")
                         )) !!}

                    </div>
                    <div class="general-row new2 col-md-4">
                        <span >New value 2: </span>

                        {!! Form::text('new2','', array('id' => 'new2', 'maxlength'=>'3','style' =>'width: 80px', 'class' => 'form-control onlyNumeric validate[required]',
                            'data-errormessage-value-missing' =>  trans("common.creatingmap_new_value2_required")
                         )) !!}
                    </div>
                </div>
            </div>
            <!-- End column 1 -->

            <!-- Begin column 2 -->
            <div class="col-md-6 " style="background-color: #FFFBD3">
                <fieldset class="panel panel-primary" style="width: 100%">
                    <legend class="legend">Fertilizer standard</legend>
                    <div class="panel-body" style="width: 100%">
                        <div class="form-inline">

                            {!! Form::select('fertilizer_standard_definition_id', $fertilizers, $initialId, array('id'=>'fertilizer_standard_definition_id', 'class' => 'form-control validate[required]', 'onchange'=>'creatingmap.getFertilizers();',
                                'data-errormessage-value-missing' =>  trans("common.creatingmap_fertilizer_required")
                            )) !!}
                            <br><span>Day la span canh: <span id="fertilizer_range"></span></span><br>
                            <label >Day la panel trong:</label>
                        </div>
                    </div>
                    <div class="panel panel-default textarea">
                        <p id="fertilizer_notes" style="margin-left: 5px">

                        </p>
                    </div>
                </fieldset>
                <fieldset class="panel panel-primary ">
                    <legend class="legend">Panel with panel-primary class</legend>
                    <label style="display: block">
                        <input type="radio" name="soil_analysis_type" id="optionsRadios1" value="1" onchange="creatingmap.changeAnalysisType();" style="margin-left: 5px"checked>
                        soil_analysis_type that&mdash;be sure to include why it's great
                    </label>
                    <label>
                        <input type="radio" name="soil_analysis_type" id="optionsRadios2" value="2" onchange="creatingmap.changeAnalysisType();" style="margin-left: 5px">
                        soil_analysis_type will deselect option one
                    </label>

                    <div id="analysis_type_2" class="form-horizontal analysis_type_2 relative">
                        <div id="analysis_type_2_cover" style="display: block;"></div>
                        <label class="control-label" style="margin-left: 5px">Option Photpho : </label>

                        {!! Form::select('p', $photphos, null, array('id'=>'p','class' => 'form-margin')) !!}
                        <br>
                        <label class="control-label" style="margin-left: 5px" >Option Kali : </label>
                        {!! Form::select('k', $kalis, null, array('id'=>'k','class' => 'form-margin')) !!}
                    </div>
                </fieldset>
                <div class="table-name">This is table 3</div>
                <table id="jqGrid3" onclick="creatingmap.setEditing();"></table>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-name">This is table 4</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-fertilizer">
                            <table id="jqGrid4"></table>
                            <div id="jqGridPager4"></div>
                            {{--<table id="jqGrid"></table>--}}
                            {{--<div id="jqGridPager"></div>--}}


                            <table id="rowed3"></table>
                            <div id="prowed3"></div>
                            <br />

                            {{--<script src="rowedex3.js" type="text/javascript"> </script>--}}
                        </div>
                    </div>
                </div>

                <div id="submit-group" class="button-group ">
                    <span><button type="button" class="button-submit" onclick="closeFancy();">{{trans('common.button_cancel')}}</button></span>
                    <span><button type="button" class="button-submit" onclick=" creatingmap.showConfirm();">{{trans('common.button_alert_ok')}}</button></span>
                </div>
            </div>
            <!-- End column 2 -->

            {!! Form::close() !!}


        </div>

        <div id="confirm-group" class="col-md-12 creatingmap-wapper total-group disable" onclick="">
            <div  style="text-align: center; "  >
                <p class="panel panel-default">
                    Tổng cộng là: <span id="total-value"></span>
                </p>
            </div>
            <div class="confirm-button-group" style="text-align: center; "  >
                <span><button type="button" class="button-submit" onclick="creatingmap.hideConfirm();">{{trans('common.button_cancel')}}</button></span>
                <span><button type="button" class="button-submit" onclick="creatingmap.submitCreatingMap();">{{trans('common.button_alert_ok')}}</button></span>
            </div>
        </div>


    </div>

    <script src="{{url('/js/modules/creatingmap.js')}}"></script>
    <script>
        creatingmap.initPage();

        resizeGrid('jqGrid1','jqGrid2','jqGrid3','jqGrid4');
    </script>
@endsection



