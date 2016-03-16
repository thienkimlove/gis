<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	
	<!-- Custom Fonts -->
    <link href="{{ url('/css/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/select2.min.css')}}" rel="stylesheet" />
	
	<style>
		#content{
			border: 1px solid black;
			width: 600px;
			height: 350px;
			margin: auto;
		}

		#div {
			margin-top: 50px;
			margin-left: 140px;
			padding: 5px;
		}

		#div1 {
			float: left;
			width: 150px;
		}

		#div2 {
			float: left;
			clear: both;
			width: 150px;
		}
	</style>
</head>
<body>
		<div id="content">
			<div style="font-family: verdana; font-size: 200%; text-align: center">
				Footer content management
			</div>
			<form action="footer-form_submit" method="post" accept-charset="utf-8">
			<div id="div">
				<div id="div1" style="font-size: 110%" >Footer content:</div>
				<div id="div1"><textarea name="footercontent" rows="10" cols="30"></textarea></div>
			</div>

			<div id="div">
				<div id="div2" style="font-size: 110%">Version:</div>
				<div id="div1" style="width: 233px"><input type="text" name="copyright" placeholder="" style="width: 230px"></div>
			</div>

			<div style="clear: both; padding: 15px; text-align: center">
				<input type="button" style="background-color: #33CCFF" name="save" value="Save">
			</div>
			</form>
		</div>
</body>
</html>

