<?php
use Gis\Http\Requests\AuthenticateRequest;
return array (
		'usernamerequired' => 'Username required',
		'username_alpha_num' => 'Username only contain alpha & number',
		'useranme_min' => "Username's length maximum is " . AuthenticateRequest::MAX_LENGTH_USERNAME,
		'password_required' => 'Password required',
		'password_alpha_num' => 'Password only contain alpha & number',
		'password_min' => "Password's length maximum is " . AuthenticateRequest::MAX_LENGTH_PASSWORD 
);
