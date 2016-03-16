<div class="form-group">
    {!! Form::label('title', 'Title') !!}
    {!! Form::text('title', null, ['class' => 'form-control']) !!}
</div>



<div class="form-group">
     {!! Form::label('desc', 'Description') !!}
     {!! Form::textarea('desc', null, ['class' => 'form-control']) !!}
</div>


<div class="form-group">
    {!! Form::label('status', 'Published') !!}
    {!! Form::checkbox('status', null, null) !!}
</div>

 <div class="form-group">
        {!! Form::submit($submitText, ['class' => 'btn btn-primary form-control']) !!}
  </div>