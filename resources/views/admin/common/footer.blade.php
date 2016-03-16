<div >
	<div style="font-family: verdana; text-align: center">
		{{ empty($footerContent) ? null : $footerContent -> content }}
		<p>
			{{ empty($footerContent) ? null : $footerContent -> version }}
		</p>
	</div>
</div>

