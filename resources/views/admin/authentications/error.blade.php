@if (Session::has('flash_notification.message'))
	<input type="hidden" value="{{ Session::get('flash_notification.message') }}" class='errorMessageApplication'/> 
	<input type="hidden" value="{{ Session::get('flash_notification.level') }}" class='levelResponseMessage' />
@endif

@if (count($errors) > 0)
    @foreach ($errors->all() as $error)
		<input type="hidden" value="{{$error}}" class='errorMessageApplication' />
	@endforeach   
@endif
	
