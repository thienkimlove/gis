<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Zukosha - Login</title>

    <!-- Custom Fonts -->
    <link href="{{ url('/css/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/select2.min.css')}}" rel="stylesheet" />


</head>

<body>

<div id="wrapper">   

    <div id="page-wrapper-login">
        <div class="login-title">Z-AGRI (SPX)</div>
        <div class="login-field">
        	<table>
        	<colgroup>
        		<col class="login-col1" width="40%"><col>
        		<col class="login-col2" width="*"><col>
        	</colgroup>
        		<tr>
        			<td>ユーザー名</td>
        			<td><input type="text" name="username" id="txtUserName"/></td>
        		</tr>
        		<tr>
        			<td>パスワード</td>
        			<td><input type="text" name="password" id="txtPassword"/></td>
        		</tr>
        		<tr>
        			<td></td>
        			<td><a>パスワードを忘れました</a></td>
        		</tr>
        	</table>
        </div>
        <div class="login-btn">
        	<span class="login-btnGuest">無料体験</span>
        	<span class="login-btnLogin">ログイン</span>
        </div>
    </div>


</div>
<script>
    var Config = {};
</script>

<script src="{{url('/js/libs/jquery/dist/jquery.min.js')}}"></script>

<script src="{{url('/js/select2.min.js')}}"></script>

</body>

</html>