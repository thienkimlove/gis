<?php
namespace Gis\Models\Services;
use Gis\Exceptions\DBApplicationException;
use Gis\Exceptions\GisException;
use Gis\Models\Repositories\HelpLinkRepositoryFacade;
use Gis\Models\SystemCode;
use Gis\Services\Logging\ApplicationLogFacade;
use Gis\Helpers\LoggingAction;
use Illuminate\Support\Facades\DB;

/**
 * Service to implement business logic to perform business regarding Help link
 * Class HelpLinkService
 * @package Gis\Models\Services
 */
class HelpLinkService extends BaseService implements HelpLinkServiceInterface{

    /**
     * Create helplink
     * @param array $data
     * @return mixed
     * @throws GisException
     */
    public function saveHelpLink(array $data) {

        if($data['popup_screen']==0) {
            $data['popup_screen']=null;
        }
        $data = $this->modifyData ( $data, true );;
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_HELP_LINK, $data);
        return HelpLinkRepositoryFacade::create ( $data );
	}

    /**
     * Find Helplink by Id
     * @param $id
     * @return mixed
     * @throws GisException
     */
    public function findHelpLinkById($id) {
        $helplink = HelpLinkRepositoryFacade::getById($id);
        if ($helplink) {
            return $helplink;
        } else {
            throw new GisException(trans('common.helplink_id_not_existed'));
        }
    }

    /**
     * Retrieve the help link by requested url
     * @param $url the requested url
     * @return mixed
     */
    function findHelpLinkByAdd($url)
    {
        $url = HelpLinkRepositoryFacade::findByField('address', $url)->first();
        return $url;
    }

    /**
     * Retrieve the help link by requested url
     * @param $url the requested url
     * @return mixed
     */
    function getHelpUrl($post)
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
        $baseUrl = str_replace("get/help","",
            strtolower($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
        );
        $currentRequestUrl = strtolower($post["url"]);
        $queryTemplateWithPopup = "select*from helplinks where (address = '%s' or address ='%s') and popup_screen =%s limit 1";
        $queryTemplateWithOutPopup = "select*from helplinks where (address = '%s' or address ='%s') limit 1";
        $query ="";
        if($post['popup'] != "0"){
            $query = $query.sprintf($queryTemplateWithPopup,$currentRequestUrl,
                    substr_replace($currentRequestUrl, "", -1),$post['popup']);
        }
        else{
            $query = $query.sprintf($queryTemplateWithOutPopup,$currentRequestUrl,
                    substr_replace($currentRequestUrl, "", -1));
        }

        $url =DB::select($query);
        if (is_null($url) || count($url)==0) {
            throw new GisException ( trans ( 'common.helplink_id_not_existed' ) ,SystemCode::NOT_FOUND);
        }
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_OPEN_HELP, sprintf("%shelps/%s",$currentRequestUrl,$url[0]->help));
        return array(
            "url"=>sprintf("%shelps/%s",$baseUrl,$url[0]->help)
        );
    }

    /**
     * Delete the help link
     * @param $ids the list of help link ids
     * @return mixed
     * @throws GisException
     */
    public function deleteHelpLink($ids) {
        try {
            HelpLinkRepositoryFacade::deleteMany ( $ids );
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DELETE_HELP_LINK, $ids);
        } catch ( \PDOException $e ) {
            if ($e->getCode () == DBApplicationException::FOREIGN_KEY_EX_CODE) {
                throw new GisException ( trans ( 'common.helplink_delete_foreign_key' ), $e->getCode () );
            } else {
                return response ()->json ( buildResponseMessage ( $e->getMessage () ) );
            }
        }
    }


    /**
     * Update Helplink
     * @param array $data
     * @param $id
     * @return mixed
     * @throws GisException
     */
    public function updateHelpLink(array $data, $id) {

        if($data['popup_screen']==0) {
            $data['popup_screen']=null;
        }
        $data = $this->modifyData ( $data );
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_HELP_LINK, $data);
		return HelpLinkRepositoryFacade::update($data, $id);
	}
}