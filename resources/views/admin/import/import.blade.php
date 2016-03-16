<?php
/**
 * User: smagic39
 * Date: 6/4/2015
 * Time: 10:56 AM
 */

?>
@extends('iframe')
@section('content')

    {!! Form::open(array('url' => route('import.data.store'),'name' => 'import-frm','class'=>'form-horizontal frm-validation-import','files'=>true))!!}
    <div class="row">
        @if ($errors->has())
            <div class="modal fade fancybox-error" id="fancybox-error" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">{{trans('common.error_title')}}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn  button-submit"
                                    data-dismiss="modal">{{trans('common.yes')}}</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div><!-- /.modal -->


        @endif
        <div class="col-md-12">
            <h2 class="page-header">{{trans('common.folder_upload_form_title')}}</h2>

            <div class="form-group">
                {!! Form::label('file_csv', trans('common.label_frm_user_map_upload_file_csv'),['class' => 'col-md-12'] ) !!}
                <div class="section-uploadfile col-md-12">
                    {!! Form::file('file_csv', ['style' => ' position: relative;text-align: right;-moz-opacity:0 ;filter:alpha(opacity: 0);opacity: 0;z-index: 2;height:0;',
                    'accept'=>'.csv','id'=>'upfile' ]) !!}
                </div>

                <div class="col-md-9">
                    <input autocomplete="off" readonly="readonly" id="displayFileName" data-prompt-position="topRight:-100" style="width: 75%;float:left;" class="form-control custom-input validate[required]" data-errormessage-value-missing="{{trans("common.usermap_import_data_input_file_required")}}" name="choose-file" type="text">
                </div>

                <div class="button-submit button-browser getFile col-md-3"
                     style="float:left;">{{trans('common.folder_upload_btn_browser')}}</div>
            </div>

            <div class="form-group">
                {!! Form::label('map_name', trans('common.label_frm_user_map_upload_map_name'),['class' => 'col-md-12'] ) !!}
                <div class="col-md-12">
                    {!! Form::text('map_name', null, ['class' => 'form-control-auto map_name custom-input validate[required,maxSize[100]]',
                    'data-prompt-position' => 'topRight:-200',
                    'data-errormessage-range-overflow' =>  trans("common.usermap_import_data_file_name_max"),
                    'data-errormessage-value-missing' =>  trans("common.usermap_import_data_file_name_required"),
                    'maxlength' => '100','placeholder' => '*',]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user_id', trans('common.label_frm_user_map_upload_user_name'),['class' => 'col-md-12'] ) !!}
                <div class="col-md-12">
                    {!! Form::text('text-auto',null,array('autocomplete' => 'off','id' => 'search-box-import',
                    'data-errormessage-value-missing' =>  trans("common.usermap_import_data_user_select_required"),
                    'data-prompt-position' => 'topRight:-200',
                    'class' => 'form-control-auto custom-input validate[required]' )) !!}
                    <ul id="search_suggestion_holder_import" class="search_suggestion_holder div-change">
                    </ul>
                </div>
                {!! Form::hidden('user_id', null, array('id' => 'user_id_import')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('user_id', trans('common.label_frm_user_map_upload_destination_folder'),['class' => 'col-md-12'] ) !!}
                <div class="col-md-12">
                    <select class="form-control-auto custom-input validate[required]" data-prompt-position="topRight:-200"
                            data-errormessage-value-missing="{{ trans('common.usermap_import_data_folder_destination_required') }}"
                            id="folder_id" name="folder_id">
                        <option value="" selected="selected"></option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }} </option>
                        @endforeach
                    </select></div>
            </div>
            <hr>
            {!! Form::button(trans('common.folder_upload_btn_save'),array('class' => 'btn-import button-submit','id'=>'btn_submit_upload')) !!}
            {!! Form::button(trans('common.button_cancel'),array('onclick'=>'parent.$.fancybox.close();','class' => 'button-submit button-reset-form')) !!}
        </div>
        {!! Form::close()!!}
    </div>

@stop

@section('footer')
    <script src="{{url('/js/modules/import_map_to_folder.js')}}"></script>
    <script>
        setTimeout(function(){
            $('.map_name').focus();
        },0);
        controlTabC('map_name','button-reset-form');
        var status = '{{ Session::get('status') }}';
        if (status.length) {
            parent.$.fancybox.close();
            parent.window.location.href = "{{ route('admin.folders.index')}}";
            location.reload(true);
        }
        $('body').click(function(event){
            var target = $( event.target );
            if(target.is('#search-box-import') && $('#search-box-import').val()){
                $('#search_suggestion_holder_import').css('display','block');
            }else $('#search_suggestion_holder_import').css('display','none');
        });
    </script>
    <script>
        applyAutocomplete(window.base_url + '/import-data/ajax-autocomplete', 'search-box-import', 'search_suggestion_holder_import', 'user_id_import');
    </script>
@endsection



