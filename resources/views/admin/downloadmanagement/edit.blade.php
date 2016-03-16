@extends('popup')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">{{trans('common.form_downloadmanagement_edit_download_title')}}</h2>
            {!! Form::model($download, ['method' => 'post', 'route' => ['admin.downloadmanagement.update'], $download->id,'class'=>'frm-validation form-horizontal form-download-update']) !!}
            <div class="form-group">
                <label class="col-md-2" for="">{{trans('common.form_downloadmanagement_is_paid_lbl')}}</label>
                <input id="paymentId" name="paymentId" type="hidden" value="{{$download->id}}">
                <input id="fertilizerMapId" name="fertilizerMapId" type="hidden" value="{{$download->fertilizer_id}}">
                <div>
                    @if($download->is_paid)
                        <input type="checkbox" id="is_paid" name="pid" value="{{$download->is_paid}}"
                               checked="checked">
                    @else
                        <input type="checkbox" id="is_paid" name="pid" value="{{$download->is_paid}}">
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12" for="">{{trans('common.form_downloadmanagement_remark_lbl')}}</label>
                <div class="col-md-12">
                    <textarea maxlength="500"  class="form-control maxSize[100]" rows="3" name="remark" id="remark"> {{$download->remark}}</textarea>

                </div>
            </div>
            <hr>
            <button class="btn-update-edit-download button-submit" id="btn-update-edit-download"
                            type="button">{{trans('common.confirm_ok')}}</button>
            <button class="button-submit " id="button-cancel" type="button" onclick="closeFancy()"
                            style="margin:10px 0 0 0;">{{trans('common.create_fertilizer_btn_cancel')}}</button>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{{asset('js/modules/download.management.action.js')}}"></script>
    <script> controlTab('is_paid','button-cancel')</script>
@endsection