<script src="{{url('messages.js')}}"></script>
<script src="{{url('/js/libs/jquery/dist/jquery.min.js')}}"></script>
<script src="{{url('/js/libs/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<script src="{{url('/js/jquery-ui.min.js')}}"></script>
<script src="{{url('/js/jquery.alerts.js')}}"></script>
<script src="{{url('/js/nav-bar.js')}}"></script>
<script src="{{url('/js/libs/metisMenu/dist/metisMenu.min.js')}}"></script>
<script src="{{url('/js/libs/raphael/raphael-min.js')}}"></script>
<script src="{{url('/js/libs/morrisjs/morris.min.js')}}"></script>
<script src="{{url('/js/libs/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js')}}"></script>
<script src="{{url('/js/select2.min.js')}}"></script>
<script src="{{url('/js/jquery_validation/jquery.validationEngine-ja.js')}}"></script>
<script src="{{url('/js/jquery_validation/jquery.validationEngine.js')}}"></script>
<script src="{{url('/js/jquery_validation/validation_form_defination.js')}}"></script>
<script src="{{url('/js/libs/bootbox.min.js')}}"></script>
<script src="{{url('/js/jquery.fancybox.js?v=2.1.5')}}"></script>
<script src="{{url('/js/common.js')}}"></script>
<script src="{{url('/js/jqgrid/grid.locale-ja.js')}}"></script>
<script src="{{url('/js/jqgrid/jquery.jqGrid.js')}}"></script>
<script src="{{url('/js/jqgrid/grid.locale-en.js')}}"></script>

<script src="{{url('/js/jqgrid/jqgrid-common.js')}}"></script>
<script src="{{url('/js/search-auto.js')}}"></script>
<script src="{{url('/js/moment.min.js')}}"></script>

<script src="{{url('/js/autocomplete.js')}}"></script>
<script src="{{asset('js/bootstrap-multiselect.js')}}"></script>
<script src="{{asset('js/jquery.mousewheel.js')}}"></script>

<script>
    //	Lang.setLocale('en');
	Lang.setLocale('jp');

    var $loading = $('#loading').hide();
    $(document)
            .ajaxStart(function () {
                 $loading.show();
            })
            .ajaxStop(function () {
                $loading.hide();
            });

    $.ajaxSetup({
        cache: false,
        async: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
    var submitAjaxRequest = function(form, event, callbackSuccess, callbackFail) {

        // Check network avaible
    	if(!isOnline()) return;

        var method = form.find('input[name="_method"]').val() ||  'POST'; //Laravel Form::open() creates an input with name _method
        var token = form.find('input[name="_token"]').val();

        var promise = $.ajax({
            url: form.attr('action'),
            data: form.serialize(),
            type:  method,
            beforeSend : function(request){

                return request.setRequestHeader('X-CSRF-Token', token);
            }
        })
                .done(function (responseData, status, xhr) {
                    // preconfigured logic for success
                })
                .fail(function (xhr, status, err) {
                    //predetermined logic for unsuccessful request
                });

        promise.then(callbackSuccess, callbackFail);

        if(event !==null) event.preventDefault();
    };
    
    ExecuteRegexExpression();
    jqgrid.setDefault();
	window.base_url ="{{ url('/') }}";
	window.permission_denined_url ="{{ url('server-error/403') }}";
	window.database_error_url ="{{ url('server-error/503') }}";
	window.error_title = Lang.get('common.error_title');
	window.info_title = Lang.get('common.info_title');
    window.mapserver= "{{(env('MAPSERVER')) ? env('MAPSERVER') : 'http://192.168.0.204/wms/'}}"
	$('input,select').keypress(function(event) { return event.keyCode != 13; }); // Disable enter key
</script>
