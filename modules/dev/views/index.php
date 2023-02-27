<?php 


	foreach ($row as $key => $value) 
	{
		echo $value['fullname'].'<br/>';
	}

	echo pagination($config);

	echo '

		<br/>
		<br/>
		
		<form action="'.site_url('home/index').'" method="post">
			<input type="text" name="fullname">

			<input type="hidden" name="step" value="post">
			<input type="submit" class="btn btn-primary" value="Submit">
		</form>';
		 
?>

<?php

		section_content('
			From Section Content <br/><br/>
		');


?>
