
<h1>{{trans('common.mailer_dear')}} {{$data['name']}} {{trans('common.mailer_after_dear')}}</h1>
<p>{{trans('common.changing_mailer_content')}}</p>
<p>{{trans('common.changinguser_lable_username')}}: {{$user->username}}</p>
<p>{{trans('common.changinguser_lable_password')}}: {{$user->password}}</p>
<p>{{trans('common.changinguser_lable_email')}}: {{$user->email}}</p>
<p>{{trans('common.changinguser_confirm_link')}}: <a href="{{$data['confirm_link']}}/{{$user->username}}/{{$user->password}}/{{$user->email}}/{{$user->token}}">{{trans('common.changinguser_confirm_text')}}</a></p>
