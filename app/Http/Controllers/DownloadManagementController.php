<?php

namespace Gis\Http\Controllers;

use Gis\Exceptions\GisException;
use Gis\Models\Repositories\FertilizerMapPaymentFacade;
use Gis\Models\Services\FertilizerService;
use Gis\Models\Services\FertilizerServiceFacade;
use Gis\Models\Services\UserServiceFacade;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\Paginator;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

class DownloadManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $groups = UserServiceFacade::getAllGroups();
        return view('admin.downloadmanagement.index', compact('groups'));
    }

    /**
     * Export the CSV file of download history
     * @return mixed
     * @throws GisException
     */
    public function getlistDataCSV(){

        $pagingRequest = Request::all();
        $postData = [
            "user_name" => $pagingRequest ['userName'],
            "user_code" => $pagingRequest ['useCode'],
            "user_group" => $pagingRequest ['userGroup'],
            "download_id" => $pagingRequest ['downloadId'],
            "download_date_start" => $pagingRequest ['downloadDateStart'],
            "download_date_end" => $pagingRequest ['downloadDateEnd'],
            "paymentState" => $pagingRequest ['paymentState'],

        ];
        if ($this->validatePostData($postData)) {
            return  FertilizerServiceFacade:: gridGetAll('mappayment', $pagingRequest, $postData, false);
        }
        return  response()->json(buildResponseMessage('', 0, null, 0));
    }
    /**
     * Export history of download to CSV file
     */
    public function exportDownload()
    {
        //Request
        $pagingRequest = Request::all();
        $postData = [
            "user_name" => $pagingRequest ['userName'],
            "user_code" => $pagingRequest ['useCode'],
            "user_group" => $pagingRequest ['userGroup'],
            "download_id" => $pagingRequest ['downloadId'],
            "download_date_start" => $pagingRequest ['downloadDateStart'],
            "download_date_end" => $pagingRequest ['downloadDateEnd'],
            "paymentState" => $pagingRequest ['paymentState'],

        ];
        $csvContent ="";
        $format = "%s\r\n";
        if ($this->validatePostData($postData)) {
            //export
            $filename = "download-" . date('Y-m-d') . ".csv";
            ini_set('auto_detect_line_endings',TRUE);
            //echo chr(0xEF) . chr(0xBB) . chr(0xBF);
            header ( 'Content-Type: text/csv; charset=utf-8' );
            header ( 'Pragma: public' ); // required
            header ( 'Expires: 0' ); // no cache
            header ( 'Content-type: application/force-download' );
            header ( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header ( 'Cache-Control: private', false );
            header ( 'Content-Disposition: attachment; filename="' . $filename . '"' ); // Add the file name
            header ( 'Content-Transfer-Encoding: binary' );
            //echo "\xEF\xBB\xBF";

            $f = fopen ( 'php://output', 'w' );
            $csvContent = $csvContent.sprintf($format,
                    trans('common.form_downloadmanagement_id').",".
                    trans('common.form_downloadmanagement_download_date_lbl').",".
                    trans('common.form_downloadmanagement_user_code_lbl').",".
                    trans('common.form_downloadmanagement_user_name_lbl').",".
                    trans('common.form_downloadmanagement_user_group_lbl').",".
                    trans('common.form_downloadmanagement_mapname_lbl').",".
                    trans('common.form_downloadmanagement_crop_lbl').",".
                    trans('common.form_downloadmanagement_mesh_size_lbl').",".
                    trans('common.form_downloadmanagement_area_a_lbl').",".
                    trans('common.form_downloadmanagement_unit_price_lbl').",".
                    trans('common.form_downloadmanagement_price_lbl').",".
                    trans('common.form_downloadmanagement_payment_lbl').",".
                    trans('common.form_downloadmanagement_remark_lbl')
                );
            $arrayList = FertilizerServiceFacade:: gridGetAll('mappayment', $pagingRequest, $postData, false);

            foreach ($arrayList as $items) {
                $fields = [];
                $fields[] = $items->download_id;
                $fields[] = $items->download_date;
                $fields[] = $items->user_code;
                $fields[] = $items->user_name;
                $fields[] = $items->user_group;
                $fields[] = $items->map_name;
                $fields[] = $items->crop_map;
                $fields[] = "$items->mesh_size m";
                $fields[] = "$items->area a";
                $fields[] = "$items->unit_price" .trans('common.total_amount_value');
                $fields[] = '"'.FertilizerServiceFacade::calculatePrice($items->unit_price , $items->area).'"'. trans('common.total_amount_value');
                $fields[] = $items->payment ? 'true' : 'false';
                $fields[] = $items->remark;
                $csvContent = $csvContent.sprintf($format,implode(",",$fields));
            }
            $tmp = $csvContent;//str_replace(PHP_EOL, "\r\n", $csvContent);
            fwrite($f, mb_convert_encoding($tmp,  'SJIS-win','UTF-8' ));
            $data = file_get_contents ( 'php://output' );
            fclose ( $f );
        }
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_EXPORT_DOWNLOAD_HISTORY,$postData);
    }


    /**
     * get download via grid
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadGrid()
    {

        $pagingRequest = Request::all();

        Paginator::currentPageResolver(function () use ($pagingRequest) {
            return $pagingRequest ['page'];
        });
        return response()->json(FertilizerServiceFacade:: gridGetAll('mappayment', $pagingRequest));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($ids)
    {
        $download = new \stdClass();
        $download->fertilizer_id = '';
        $download->is_paid = false;
        $download->remark = '';
        $lids = explode(',', $ids);

        if (count($lids) == 2) {
            $download = FertilizerMapPaymentFacade::find($lids[0]);
        }else{
            $download->id = $ids;
        }
        return view('admin.downloadmanagement.edit', compact('download'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update()
    {
        $request = Request::all();
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_UPDATE_DOWNLOAD_HISTORY,$request);
        return FertilizerServiceFacade::activePayment($request);
    }

    /**
     * Search data for download history
     * @return mixed
     * @throws GisException
     */
    public function searchDownload()
    {
        $pagingRequest = Request::all();
        Paginator::currentPageResolver(function () use ($pagingRequest) {
            return $pagingRequest ['page'];
        });
        $postData = [
            "user_name" => $pagingRequest ['userName'],
            "user_code" => $pagingRequest ['useCode'],
            "user_group" => $pagingRequest ['userGroup'],
            "download_id" => $pagingRequest ['downloadId'],
            "download_date_start" => $pagingRequest ['downloadDateStart'],
            "download_date_end" => $pagingRequest ['downloadDateEnd'],
            "paymentState" => $pagingRequest ['paymentState'],

        ];
        if ($this->validatePostData($postData)) {
            return response()->json(FertilizerServiceFacade:: gridGetAll('mappayment', $pagingRequest, $postData));
        }
    }

    /**
     * Validate search condition
     * @param $postData
     */
    function validatePostData($postData)
    {
        //common_date_invalid
        if (!$this->isValidDateTimeString($postData["download_date_start"])) {
            throw new GisException(trans("common.common_date_invalid"));
        }
        if (!$this->isValidDateTimeString($postData["download_date_end"])) {
            throw new GisException(trans("common.common_date_invalid"));
        }
        return true;
    }

    /**
     * Check if a string is a valid date(time)
     * @param $strDate
     * @return bool
     */
    function isValidDateTimeString($strDate)
    {
        if (empty($strDate)) {
            return true;
        }
        return (date('Y-m-d', strtotime($strDate)) == $strDate);
    }

}
