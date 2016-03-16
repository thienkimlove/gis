@extends('login')

@section('content')
<style>
body{
font-size: 13px;
}
</style>
<link href="{{asset('css/term.css')}}" rel="stylesheet">
{!! Form::open(array('url' => 'submitTerm', 'method' => 'POST', 'class' => 'frm-term-submit')) !!}

<div class="term-centered tou-parent-layout col-md-8 col-sm-10 col-xs-11">
<input type="hidden" name="is_agree" id="is_agree" />
@if(session('user')->usergroup->is_guest_group==true)
    <h2 style="text-align: center">{{ trans('common.termofuser_label_guest_msg') }}</h2>
    <div class="form-group col-xs-offset-4">
    		<button type="submit" agreed="1" class=" agree-term button-submit click-submit">{{ trans('common.button_alert_ok') }}</button>
    		<button type="submit" agreed="0" class=" disagree-term button-submit click-submit col-xs-offset-1">{{ trans('common.button_cancel') }}</button>

    		<div style="clear: both;"></div>
    </div>
@else
    <p>{{ trans('common.termofuser_label_term_use_1') }}</p>
    <div id="holder" class="term-holder"></div>
	<p>{{ trans('common.termofuser_label_term_use_2') }}</p>
    <div class="">
		<button type="submit" agreed="0" class="btnCancelTou login-btnGuest disagree-term white-button click-submit">{{ trans('common.termofuser_disagree_button') }}</button>
		<button type="submit" agreed="1" class="btnOkTou agree-term button-submit click-submit">{{ trans('common.termofuser_agree_button') }}</button>

		<div style="clear: both;"></div>
	</div>
@endif
</div>


{!! Form::close() !!}
@endsection

@section('footer')
@if(session('user')->usergroup->is_guest_group!=true)
<script type="text/javascript" src="{{url('js/libs/pdf/build/pdf.js')}}"></script>
<script type="text/javascript" src="{{url('js/libs/pdf/web/compatibility.js')}}"></script>
<script type="text/javascript" src="{{url('js/modules/term_of_use.js')}}"></script>
@endif
<script type='text/javascript'>
		$(function(){
			 $('.click-submit').click(function(event){
                    event.preventDefault();
                    $('#is_agree').val($(this).attr('agreed'));
                    gisForm.clickSave(event, {
                        formEle : $('.frm-term-submit'),
                        callbackFunction : function(data){
                            if (data.code == 200) {
                                window.location = window.base_url;
                            } else {
                                fancyAlert(data.message, Lang.get('common.error_title'));
                            }
                        }
                    });
                });
		});
	</script>
@endsection
