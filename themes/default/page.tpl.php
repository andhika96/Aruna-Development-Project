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
			<link rel="stylesheet" href="'.base_url('assets/plugins/bootstrap/5.1.3/css/bootstrap.min.css').'">

			<!-- Font Lato CSS -->
			<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap">

			<!-- Custom CSS -->
			<link rel="stylesheet" href="'.base_url('assets/css/aruna-v3.css').'">

			<title>Aruna Development Project</title>
		</head>

		<body>');

	// Load application from modules
	display_application_content();

	display_application_footer('
			<!-- Optional JavaScript -->
			<!-- jQuery first, then Popper.js, then Bootstrap JS, and other -->
			<script src="'.base_url('assets/js/jquery-3.7.0.min.js').'"></script>
			<script src="'.base_url('assets/plugins/bootstrap/5.1.3/js/bootstrap.bundle.min.js').'"></script>
			<script src="'.base_url('assets/plugins/fontawesome/5.15.1/js/all.min.js').'"></script>
		</body>
	</html>');

?>