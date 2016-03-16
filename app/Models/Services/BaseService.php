<?php

namespace Gis\Models\Services;
use Carbon\Carbon;
use Gis\Models\Repositories\FertilizerMapPaymentFacade;
use Gis\Models\Repositories\GroupFacade;
use Gis\Models\Repositories\UserFacade;
use Gis\Models\Repositories\FertilizationPriceFacade;
use Gis\Models\Repositories\StandardCropFacade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Gis\Models\Repositories\HelpLinkRepositoryFacade;

/**
 * Handle common business for overall other services
 * Class BaseService
 * @package Gis\Models\Services
 */
class BaseService implements BaseServiceInterface
{

    CONST USER_LIMIT_PER_PAGE = 10;
    CONST GROUP_LIMIT_PER_PAGE = 10;
    CONST PAYMENT_LIMIT_PER_PAGE = 10;
    CONST HELPLINK_LIMIT_PER_PAGE = 10;
    CONST PRICE_LIMIT_PER_PAGE = 10;

    /**
     * Modify the data before performing a business
     * @param array $attributes
     * @param bool|false $create
     * @return array
     */
    function modifyData(array $attributes, $create = false)
    {
        $currentUserCode = (Session::has('user')) ? Session::get('user')->user_code : null;
        if ($create) {
            $attributes['ins_user'] = $currentUserCode;
            $attributes['ins_time'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $attributes ['upd_user'] = $currentUserCode;
        $attributes ['upd_time'] = Carbon::now()->format('Y-m-d H:i:s');

        return $attributes;
    }

    /**
     * modify an object before performing a business
     * @param $object
     * @param bool|false $create
     * @return mixed
     */
    function modifyObject($object, $create = false)
    {
        $currentUserCode = (Session::has('user')) ? Session::get('user')->user_code : null;
        if ($create) {
            $object->ins_user = $currentUserCode;
            $object->ins_time = Carbon::now()->format('Y-m-d H:i:s');
        }
        $object->upd_user = $currentUserCode;
        $object->upd_time = Carbon::now()->format('Y-m-d H:i:s');

        return $object;
    }

    /**
     * Filter users by conditions.
     * 
     * @param
     *            $post
     * @return array
     */
    private function _generateConditions($post)
    {
        $conditions = array();
    			
        if ($post) {
            foreach ($post as $key => $value) {
                if (empty($value)) {
                    if ($value !== '0')
                        continue;
                }
                
                if ($key === 'user_locked_flg') {
                    $operator = '=';
                    $value = $value === 'f' ? false : true;
                } elseif ($key === 'username' || $key === 'email') {
                    $operator = 'like';
                    $value = "%" . $value . "%";
                } else {
                    $operator = '=';
                }
                $conditions[] = array(
                    $key,
                    $operator,
                    $value
                );
            }
        }
        
        return $conditions;
    }

    /**
     * Build a search condition
     * @param $post
     * @return array
     */
    private function getConditions($post)
    {
    	$conditions = array();
    	if ($post) {
    		foreach ($post as $key => $value) {
    			if (empty($value)) {
    				if ($value !== '0')
    					continue;
    			}
   			     			
    			if ($key === 'username') {
    				$operator = 'like';
    				$value = "%" . $value . "%";
    			}elseif ($key === 'user_code') {
    				$operator = '=';
    			}elseif ($key =='group_id'){
    				$key = 'user_group_id';
    				$operator = '=';
    			}elseif ($key === 'fertilizer_id'){
    				$fertilizer = FertilizerServiceFacade::getById($value);
    				$key = 'user_code';
    				$operator = '!=';
    				$value = $fertilizer->ins_user;
    			}
    			
    			$conditions[] = array(
    					$key,
    					$operator,
    					$value
    			);
    		}
    	}
    
    	return $conditions;
    }

    /**
     * Build a condition to get crops
     * @param $post
     * @return array
     */
    private function getStandardCropConditions($post)
    {
    	$conditions = array();
    	if ($post) {
    		foreach ($post as $key => $value) {
    			if (empty($value)) {
    				if ($value !== '0')
    					continue;
    			}
    
    			if ($key === 'fertilizer_standard_definition_id') {
    				$operator = '=';
    			}
    			$conditions[] = array(
    					$key,
    					$operator,
    					$value
    			);
    		}
    	}
    
    	return $conditions;
    }

    /**
     * Get grid Record.
     * @param $resource
     * @param null $pagingRequest
     * @param array $postData
     * @return array
     * @internal param $default
     * @internal param null $limit
     */
    public function gridGetAll($resource, $pagingRequest = null, $postData = array())
    {

        switch ($resource) {

            case 'groups' :
                $limit = empty($pagingRequest['rows']) ? self::GROUP_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $records = GroupFacade::orderBy('group_name')->paginate($limit);
                break;
            case 'mappayment' :
                $limit = empty($pagingRequest['rows']) ? self::PAYMENT_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $records = FertilizerMapPaymentFacade::selectModel()->has('fertilizer_maps')->orderBy('payment_date')->paginate($limit);
                break;
            case 'helplink' :
                $limit = empty($pagingRequest['rows']) ? self::HELPLINK_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $records = HelpLinkRepositoryFacade::orderBy('address')->paginate($limit);
                break;
            case 'users' :
                $limit = empty($pagingRequest['rows']) ? self::USER_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $conditions = $this->_generateConditions($postData);
                
                if($pagingRequest['sidx']=='') $pagingRequest['sidx'] = 'username';   
                $records = UserFacade::with('usergroup')->whereConditions($conditions)
                ->join('usergroups', 'usergroups.id', '=', 'users.user_group_id')
                ->select('users.*', 'usergroups.group_name')
                ->orderBy($pagingRequest['sidx'],$pagingRequest['sord'])->paginate($limit);
                foreach($records->items()  as $key){
                    $key->email = strlen($key->email) <=216 ? $this->decode($key->email) : $key->email;
                }
                break;            
                if ($conditions) {
                    $records = UserFacade::with('usergroup')->whereConditions($conditions)->orderBy('username')->paginate($limit);
                } else {
                    $records = UserFacade::with('usergroup')->orderBy('username')->paginate($limit);
                }
                break;
            case 'specifyusers' :

                $limit = empty($pagingRequest['rows']) ? self::USER_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $conditions = $this->getConditions($postData);

                $records = UserFacade::with('usergroup')->whereConditions($conditions)
                //->where('username','!=',session('user')->username)
                ->orderBy('username',$pagingRequest['sord'])->paginate($limit);
                break;
                
            case 'standartcrops' :
                $limit = $pagingRequest['rows'];
                $conditions = $this->getStandardCropConditions($postData);

                //return  StandardCropFacade::with('crop')->whereConditions($conditions)->orderBy('id')->toSql();
                $records = StandardCropFacade::with('crop')->whereConditions($conditions)
                    ->join('crops_definitions', 'crops_definitions.id', '=', 'user_fertilizer_definition_details.crops_id')
                    ->select('user_fertilizer_definition_details.*')
                    ->orderBy('order_number')
                    ->paginate($limit);
                foreach ( $records as $obj ) {
                	$obj->remarks =  htmlspecialchars($obj->remarks);
                }
                break;
            case 'fertilizer_unit_price':
                $limit = empty($pagingRequest['rows']) ? self::PRICE_LIMIT_PER_PAGE : $pagingRequest['rows'];
                $records = FertilizationPriceFacade::orderBy('start_date')->paginate($limit);
                break;
        }
        return array(
            'page' => $records->currentPage(),
            'total' => $records->lastPage() ? $records->lastPage() : 1,
            'records' => $records->total(),
            'rows' => $records->items()
        );
    }

    /**
     * @param $string
     *
     * @return mixed|string
     */
    public function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);

        return $data;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

    /**
     * @param $value
     *
     * @return bool|string
     */
    public function encode($value)
    {
        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, Config::get('app.key'), $text, MCRYPT_MODE_ECB, $iv);

        return trim($this->safe_b64encode($crypttext));
    }

    /**
     * @param $value
     *
     * @return bool|string
     */
    public function decode($value)
    {
        if (!$value) {
            return false;
        }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, Config::get('app.key'), $crypttext, MCRYPT_MODE_ECB, $iv);

        return trim($decrypttext);


    }

}