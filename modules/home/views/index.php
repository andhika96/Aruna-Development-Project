<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 */

	defined('MODULEPATH') OR exit('No direct script access allowed');

	section_content('
	<!-- Custom CSS -->
	<link rel="stylesheet" href="'.base_url('assets/css/default-homepage.css').'">

	<div class="container d-flex align-items-center vh-100 my-4 my-lg-0">
		<div>
			<div class="ar-default-homepage bg-white shadow mb-3">
				<div class="row">
					<div class="col-lg-7 order-2 order-lg-1 d-flex align-items-center text-center">
						<div>
							<h3 class="mb-3">Welcome to Aruna Development Project v4.5</h3>
							<p class="mb-4 mb-md-5">The page you are looking at is being generated dynamically by Aruna Development Project.</p>
							<h4>Thanks for using my Framework !! <i class="far fa-smile ml-1"></i></h4>
						</div>
					</div>

					<div class="col-lg-5 mb-5 mb-lg-0 order-1 order-lg-2 text-center">
						<img src="'.base_url('assets/images/undraw_love_re_mwbq.svg').'" class="img-fluid">
					</div>
				</div>
			</div>

			<div class="row text-white">
				<div class="col-lg-6 col-md-6 text-center text-md-start mb-3 mb-md-0">
					Made with <i class="fas fa-heart mx-1"></i> & <i class="fas fa-coffee mx-1"></i> in Jakarta, Indonesian. 2023
				</div>

				<div class="col-lg-6 col-md-6 text-center text-md-end">
					Created & Developed by <a href="https://www.instagram.com/andhika_adhitia" target="_blank" class="text-white font-weight-bold"><u>Andhika Adhitia N</u></a>
				</div>
			</div>
		</div>
	</div>');

?>