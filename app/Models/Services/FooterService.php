<?php
namespace Gis\Models\Services;

use Gis\Models\Repositories\FooterRepositoryFacade;
use Illuminate\Contracts\View\View;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;
/**
 * The service to handle business logic regarding footer content
 * Class FooterService
 * @package Gis\Models\Services
 */
class FooterService extends BaseService implements FooterServiceInterface{


    /**
     * retrieve the footer content
     * @return mixed
     */
    public function loadFooter() {
		return FooterRepositoryFacade::all()->first();
	}

    /**
     * Support to retrieve the footer content
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with([
            'footerContent' => FooterRepositoryFacade::all()->first()
        ]);
    }

    /**
     * Save footer content to the database
     * @param array $attributes
     */
    public function saveFooter(array $attributes) {
        $footer = $this->loadFooter();
        if ($footer) {
            $attributes = $this->modifyData($attributes);
            $footer->update($attributes);
        } else {
            $attributes = $this->modifyData($attributes, true);
            FooterRepositoryFacade::create($attributes);
        }
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_FOOTER_CONTENT, $attributes);
	}


}