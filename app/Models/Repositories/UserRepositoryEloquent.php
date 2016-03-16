<?php
namespace Gis\Models\Repositories;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * User repository provider functional access to database.It like same data provider layer.
 * Class UserRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class UserRepositoryEloquent extends GisRepository implements UserRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\User';
    }

    /**
     * Check login
     *
     * @param array $data            
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function checkLogin(array $data)
    {
        if (! empty($data['email'])) {
            $where = array(
                'email' => Crypt::encrypt($data['email']),
                'password' => $data['password']
            );
        } else {
            $where = array(
                'username' => $data['username'],
                'password' => $data['password']
            );
        }
        return $this->model->where($where)->first();
    }

    /**
     * Find data by multiple fields
     *
     * @param array $ids            
     * @param array $columns            
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserLogDataByIds($ids, $columns = array( '*' ))
    {
        return $this->model->whereIn('id', $ids)->get($columns);
    }

    /**
     * Get User in group guest
     *
     * @return Gis\Models\Entities\User
     */
    public function getAccountGuest()
    {
        return $this->model->whereHas('usergroup', function ($query)
        {
            $query->where('usergroups.is_guest_group', '=', true);
        });
    }

    public function getSpecifyUsers($limit, $orderBy, $orderType)
    {
        $result = $this->model->orderBy($orderBy, $orderType);
        return empty($limit) ? $result->get() : $result->paginate($limit);
    }

    public function getWithOutAdminAndGuest($keyword = null)
    {
        $users = $this->model->whereHas('usergroup', function ($query)
        {
            $query->where('usergroups.auth_authorization', '=', false);
        })
            ->get();
        $result = array();
        foreach ($users as $user) {
            array_push($result, $user->id);
        }
        $i = 0;
        $users_result = DB::table('users')->where('username', 'ilike', '%' . $keyword . '%')
            ->orWhere('user_code', '=', (int) $keyword)
            ->get();
        $user_list = array();
        if (! empty($users_result)) {
            foreach ($users_result as $user) {
                if (in_array($user->id, $result)) {
                    array_push($user_list, $user);
                    $i++;
                }
                if ($i==10) break;
            }
        }
        return $user_list;
    }

    public function getLimitFertilizers($keyword = null)
    {
        $result = $this->model->where('fertilization_standard_name', 'like', '%' . $keyword . '%')->orderBy('fertilization_standard_name');
        return $result->paginate(15);
    }
}