@extends('admin')
@section('content')
    <link href="{{ asset('/css/user.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <h4 class="panel-heading">{{ trans('common.footer_title_page') }}</h4>
                    <div class="panel-body">
                        {!! Form::model($footer, array(
                        'url' => 'saveFooter',
                        'method' => 'POST',
                        'class' => 'frm-validation-footer form-horizontal'
                        )) !!}

                        <div class="form-group">
                            <label class="col-md-2" style="margin-top: 4px;">{{ trans('common.footer_label_content') }}</label>
                            <div class="col-md-10">
                                {!! Form::text('content', null, array(
                                'maxlength'=>'300',
                                'placeholder' => '*',
                                'class' => 'form-control validate[required,maxSize[300]]',
                                'data-prompt-position' => 'topRight:-250',
                                'data-errormessage-range-overflow' =>  trans("common.footer_check_content_input_max"),
                                'data-errormessage-value-missing' =>  trans("common.footer_required_content_field")
                                )) !!}
                            </div>
                        </div>
                        <div class="form-group">

                            <label class="col-md-2" style="margin-top: 4px;">{{ trans('common.footer_label_version') }}</label>
                            <div class="col-md-10">
                                {!! Form::text('version', null, array(
                                'maxlength'=>'50',
                                'placeholder' => '*',
                                'class' => 'form-control validate[required,maxSize[50]]',
                                'data-prompt-position' => 'topRight:-250',
                                'data-errormessage-range-overflow' =>  trans("common.footer_check_version_input_max"),
                                'data-errormessage-value-missing' =>  trans("common.footer_required_version_field")
                                )) !!}
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12" style="text-align: center;">
                                {!! Form::button(trans('common.button_save'), array(
                                'class' => 'button-submit btn-save-footer',
                                'type' => 'button',
                                )) !!}
                                <button class="button-submit" type="button" onclick="location.href=window.base_url;">{{trans('common.button_cancel')}}</button>
                        </div>
                        {!! Form::close() !!}
                    <div class="container-fluid">
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script>
        $(function(){
            $('.btn-save-footer').click(function(event){

                var titleMsg = Lang.get('common.error_title');
                var titleSuccess = Lang.get('common.info_title');

                gisForm.clickSave(event, {
                    formEle : $('.frm-validation-footer'),
                    callbackFunction : function(data){
                        if (data.code == 200) {
                            fancyAlertAndLoadPage(Lang.get('common.footer_create_success'), titleSuccess);

                        } else {
                            fancyAlertAndLoadPage(data.message, titleMsg);
                        }
                    }
                });

                
            });
        });
    </script>
    <script>
    controlTabC('navbar-brand','btnCancelFooter');
    </script>
@endsection

