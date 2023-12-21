@extends('email_templates.layouts.app_email')
	@section('content')
	  
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				@php
				echo '<pre>';
                print_r($response);
				@endphp
			
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				@php
				echo '<pre>====================================================';
                print_r($mailMessage);
				@endphp
			</td>
		</tr>
	</table>
    
  @endsection