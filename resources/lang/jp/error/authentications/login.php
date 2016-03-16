<?php
use Gis\Http\Requests\AuthenticateRequest;
return array (
        'username_required' => 'Username required',
        'username_alpha_num' => 'Username only contain alpha & number',
        'username_max' => "Username's length maximum is " . AuthenticateRequest::MAX_LENGTH_USERNAME,
        'password_required' => 'Password required',
        'password_alpha_num' => 'Password only contain alpha & number',
        'password_max' => "Password's length maximum is " . AuthenticateRequest::MAX_LENGTH_PASSWORD,
        'user_not_found' => 'invalid username or password',
        'user_is_locked' => 'User is locked, please contact the administrator',
        'login_limit' => 'Login pass limit', 
);
