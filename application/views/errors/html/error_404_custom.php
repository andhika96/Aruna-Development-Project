<?php

	defined('BASEPATH') OR exit('No direct script access allowed');

	echo '
	<!doctype html>
	<html lang="en">
		<head>
			<!-- Required meta tags -->
			<meta charset="utf-8">
			<meta name="robots" content="noindex">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

			<!-- Bootstrap CSS -->
			<link rel="stylesheet" href="'.base_url('assets/plugins/bootstrap/4.6.0/css/bootstrap.min.css').'">

			<!-- Custom Font CSS -->
			<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap">

			<!-- Custom CSS -->
			<style>
			body 
			{
				margin: 0;
				font-family: \'Nunito\', -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
				font-size: 14px;
				font-weight: 400;
				line-height: 1.5;
				color: #212529;
				text-align: left;
				background-color: #fff;
			}
			</style>

			<title>404 Page Not Found</title>
		</head>
		
		<body>
			<div class="container">
				<div class="d-flex justify-content-center align-items-center vh-100">
					<div>
						<div class="row mb-4">
							<div class="col-6 d-flex justify-content-end align-items-center" style="padding-right: 1rem">
								<h1 class="font-weight-bold mb-0">404</h1>
							</div>

							<div class="col-6 d-flex justify-content-start align-items-center" style="padding-left: 1rem;border-left: 4px #333 solid">
								<i class="fas fa-heart-broken fa-3x" style="color: #e91e63"></i>
							</div>
						</div>

						<h5 class="mb-0 text-center">Sorry sweetheart, I can\'t find the page you requested <i class="fas fa-frown ml-1 fa-lg" style="color: #3f51b5"></i></h5>

						<div class="text-center" style="margin-top: 2rem">
							<a href="javascript:history.back();"><i class="fas fa-long-arrow-alt-left mr-2"></i> Back to Previous Page</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Optional JavaScript -->
			<!-- jQuery first, then Popper.js, then Bootstrap JS -->
			<script src="'.base_url('assets/js/jquery-3.6.0.slim.min.js').'"></script>
			<script src="'.base_url('assets/plugins/bootstrap/4.6.0/js/bootstrap.bundle.min.js').'"></script>
			<script src="'.base_url('assets/plugins/fontawesome/5.15.1/js/all.min.js').'"></script>
		</body>
	</html>';

?>