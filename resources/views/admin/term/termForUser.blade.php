@extends('login')

@section('content')
<style>
body{
font-size: 13px;
}
</style>
<link href="{{asset('css/term.css')}}" rel="stylesheet">
<div class="term-centered tou-parent-layout col-md-8 col-sm-10 col-xs-11">
<input type="hidden" name="is_agree" id="is_agree" />
    <p>{{ trans('common.termofuser_label_term_use_1') }}</p>
    <div id="holder" class="term-holder"></div>
	<p>{{ trans('common.termofuser_label_term_use_2') }}</p>
</div>


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
