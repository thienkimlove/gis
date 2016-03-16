@extends('dialog')
@section('content')
    <title>{{trans('common.out_predict_popup_title')}}</title>
    <style>
        table, th, td {
            border: 1px solid black;
            text-align: center;
        }
        .footer-custom-me{
            padding: 5px;
        }
    </style>
    {!! Form::model(array(
    'class' => 'form-horizontal form-buy-fertilizer'
    )) !!}
    <div class="modal-body">
        <div style="width: 100%;text-align: center;">
            <div class="col-xs-2"></div>
            <div class="col-xs-2"></div>
            <div class="col-xs-3" style="margin-left: -15px;"></div>
        </div>
        @if($barrel_type ==2)
        <table class="table table-fertilization-predict" style="font-size: 13px;">
            <tbody id="rowdata">
            <tr style="border: 1px solid black;color: #000000;">
                <td>{{ trans('common.out_predict_popup_header') }}</td>
                <td colspan="2" id="maxMain" ></td>
                <td colspan="2" id="maxSub"></td>
                <td></td>
            </tr>
            <tr style="color: red;">
                <td rowspan="2" style="vertical-align: middle;">{{ trans('common.out_predict_popup_table_column_1.1') }}</td>
                <td colspan="2">{{ trans('common.out_predict_popup_table_column_1.2') }}</td>
                <td colspan="2">{{ trans('common.out_predict_popup_table_column_1.3') }}</td>
                <td rowspan="2" style="vertical-align: middle;">{{ trans('common.out_predict_popup_table_column_1.4') }}</td>
            </tr>
            <tr style="color: red;">
                <td>{{ trans('common.out_predict_popup_table_row_2.1') }}</td>
                <td>{{ trans('common.out_predict_popup_table_row_2.2') }}</td>
                <td>{{ trans('common.out_predict_popup_table_row_2.1') }}</td>
                <td>{{ trans('common.out_predict_popup_table_row_2.2') }}</td>
            </tr>
            </tbody>
        </table>
        @endif
        @if($barrel_type ==1)
            <table class="table table-fertilization-predict" style="font-size: 13px;">
                <tbody id="rowdata">
                <tr style="border: 1px solid black;color: #000000;">
                    <td>{{ trans('common.out_predict_popup_header') }}</td>
                    <td colspan="2" id="maxMain" ></td>
                    <td></td>
                </tr>
                <tr style="color: red;">
                    <td rowspan="2" style="vertical-align: middle;">{{ trans('common.out_predict_popup_table_column_1.1') }}</td>
                    <td colspan="2">{{ trans('common.out_predict_popup_table_column_1.2') }}</td>
                    <td rowspan="2" style="vertical-align: middle;">{{ trans('common.out_predict_popup_table_column_1.4') }}</td>
                </tr>
                <tr style="color: red;">
                    <td>{{ trans('common.out_predict_popup_table_row_2.1') }}</td>
                    <td>{{ trans('common.out_predict_popup_table_row_2.2') }}</td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>

    <div class="modal-footer footer-custom-me" style="margin-top: -20px;">
        <button class="button-submit btn-cancel-popup" type="button" style="margin-top:7px;" >{{trans('common.usergroup_add_close_title')}}</button>
    </div>
    {!!Form::close()!!}
@endsection
@section('footer')
    <script>
//        gisMap.appendHtml();
    $(function() {
        $('.btn-cancel-popup').click(function (event) {
            event.preventDefault();
            closeDialog();
        });
    });
    </script>
@endsection