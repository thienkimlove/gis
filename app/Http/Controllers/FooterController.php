<?php
namespace Gis\Http\Controllers;

use Gis\Models\Services\FooterServiceFacade;
use Gis\Http\Requests\FooterRequest;
use Gis\Models\SystemCode;

/**
 * Use this class to handle all the businesses regarding the footer content
 * Class FooterController
 * @package Gis\Http\Controllers
 */
class FooterController extends CoreController
{
	/**
	 * Show the footer information to the browser
	 * @return the information of footer
	 */
	public function index()
    {
		$footer = FooterServiceFacade::loadFooter();
		return view('admin.footer.index', compact('footer'));
	}

	/**
	 * Save the information of footer to the database
	 * @param FooterRequest $request the inforamtion of footer to save
	 * @return the message that indicates the processing is successful or not
	 */

	public function saveFooter(FooterRequest $request){

        FooterServiceFacade::saveFooter($request->all());

		return response()->json(buildResponseMessage(
            trans('common.footer_create_success'),
            SystemCode::SUCCESS
        ));
	}
}