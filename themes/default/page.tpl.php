<?php

	defined('THEMEPATH') OR exit('No direct script access allowed');
	
	display_application_header('
	<!doctype html>
	<html lang="en">
		<head>
			<!-- Required meta tags -->
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

			<!-- Bootstrap CSS -->
			<link rel="stylesheet" href="'.base_url('assets/plugins/bootstrap/4.4.1/css/bootstrap.min.css').'">

			<!-- Font Lato CSS -->
			<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet"> 

			<!-- jQuery UI CSS -->
			<link rel="stylesheet" href="'.base_url('assets/css/jquery-ui.min.css').'">

			<!-- Custom CSS -->
			<link rel="stylesheet" href="'.base_url('assets/css/aruna.css').'">

			<title>Aruna Development Project</title>
		</head>

		<body>');

	// Load application from modules
	display_application_content();

	display_application_footer('
			<!-- Optional JavaScript -->
			<!-- jQuery first, then Popper.js, then Bootstrap JS, and other -->
			<script src="'.base_url('assets/js/jquery-3.4.1.min.js').'"></script>
			<script src="'.base_url('assets/js/jquery-ui-1.12.1.min.js').'"></script>
			<script src="'.base_url('assets/js/popper.min.js').'"></script>
			<script src="'.base_url('assets/plugins/bootstrap/4.4.1/js/bootstrap.min.js').'"></script>
			<script src="'.base_url('assets/plugins/fontawesome/5.13.0/js/all.min.js').'"></script>
		</body>
	</html>');

?>