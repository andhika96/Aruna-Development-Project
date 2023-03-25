<?php

	defined('BASEPATH') OR exit('No direct script access allowed');

	load_ext(['url']);

	//--------------------------------------------------------------------

	/**
	 * Creates a syntax-highlighted version of a PHP file.
	 *
	 * @param string  $file
	 * @param integer $lineNumber
	 * @param integer $lines
	 *
	 * @return boolean|string
	 */

	function highlightFile(string $file, int $lineNumber, int $lines = 15)
	{
		if (empty($file) || ! is_readable($file))
		{
			return false;
		}

		// Set our highlight colors:
		if (function_exists('ini_set'))
		{
			ini_set('highlight.comment', '#767a7e; font-style: italic');
			ini_set('highlight.default', '#c7c7c7');
			ini_set('highlight.html', '#06B');
			ini_set('highlight.keyword', '#f1ce61;');
			ini_set('highlight.string', '#869d6a');
		}

		try
		{
			$source = file_get_contents($file);
		}
		catch (Throwable $e)
		{
			return false;
		}

		$source = str_replace(["\r\n", "\r"], "\n", $source);
		$source = explode("\n", highlight_string($source, true));
		$source = str_replace('<br />', "\n", $source[1]);

		$source = explode("\n", str_replace("\r\n", "\n", $source));

		// Get just the part to show
		$start = $lineNumber - (int) round($lines / 2);
		$start = $start < 0 ? 0 : $start;

		// Get just the lines we need to display, while keeping line numbers...
		$source = array_splice($source, $start, $lines, true);

		// Used to format the line number in the source
		$format = '% ' . strlen(sprintf('%s', $start + $lines)) . 'd';

		$out = '';
		// Because the highlighting may have an uneven number
		// of open and close span tags on one line, we need
		// to ensure we can close them all to get the lines
		// showing correctly.
		$spans = 1;

		foreach ($source as $n => $row)
		{
			$spans += substr_count($row, '<span') - substr_count($row, '</span');
			$row    = str_replace(["\r", "\n"], ['', ''], $row);

			if (($n + $start + 1) === $lineNumber)
			{
				preg_match_all('#<[^>]+>#', $row, $tags);
				$out .= sprintf("<span class='line highlight'><span class='number'>{$format}</span> %s\n</span>%s", $n + $start + 1, strip_tags($row), implode('', $tags[0])
				);
			}
			else
			{
				$out .= sprintf('<span class="line"><span class="number">' . $format . '</span> %s', $n + $start + 1, $row) . "\n";
			}
		}

		if ($spans > 0)
		{
			$out .= str_repeat('</span>', $spans);
		}

		return '<pre><code>' . $out . '</code></pre>';
	}

	echo '
	<!doctype html>
	<html>
	<head>
		<meta charset="UTF-8">
		<meta name="robots" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="'.base_url('assets/plugins/bootstrap/4.6.0/css/bootstrap.min.css').'">

		<!-- Font Lato CSS -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap">

		<!-- Custom CSS -->
		<link rel="stylesheet" href="'.base_url('assets/css/default-homepage.css').'">

		<!-- Font Awesome -->
		<script src="'.base_url('assets/plugins/fontawesome/5.15.1/js/all.min.js').'"></script>

		<style>
		body 
		{
			font-size: 14px;
			background-color: #f44336 !important;
		}
		</style>
	</head>

	<body>
		<div class="container d-flex align-items-center my-5">
			<div class="w-100">
				<div class="ar-default-homepage bg-white shadow mb-3 p-5">
					<div class="row">
						<div class="col-12 d-flex align-items-center">
							<div class="w-100">
								<div class="h3 pb-3 mb-3 border-bottom">An uncaught Exception was encountered</div>
								<p class="mb-2">Type: '.get_class($exception).'</p>
								<p class="mb-2">Message: '.$message.'</p>
								<p class="mb-2">Filename: '.$exception->getFile().'</p>
								<p class="mb-2">Line Number: '.$exception->getLine().'</p>

								<div class="source mt-4">
									'.highlightFile($exception->getFile(), $exception->getLine()).'
								</div>

								<div class="mt-5">
									<h5 class="mb-3">Backtrace</h5>';

							if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE)
							{
								foreach ($exception->getTrace() as $error)
								{
									echo '
									<hr class="mb-3 mt-4" style="color: #000;background-color: #000">
									<p class="mb-2">Error: '.$error['file'].'</p>
									<p class="mb-2">Line: '.$error['line'].'</p>
									<p class="mb-2">Function: '.$error['function'].'</p>
									
									<div class="source">
										'.highlightFile($error['file'], $error['line']).'
									</div>';
								}
							}

	echo '
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row text-white">
					<div class="col-lg-6 col-md-6 text-center text-md-left mb-3 mb-md-0">
						Made with <i class="fas fa-heart mx-1"></i> & <i class="fas fa-coffee mx-1"></i> in Jakarta, Indonesian.
					</div>

					<div class="col-lg-6 col-md-6 text-center text-md-right">
						Created & Developed by <a href="https://www.instagram.com/andhika_adhitia" target="_blank" class="text-white font-weight-bold"><u>Andhika Adhitia N</u></a>
					</div>
				</div>
			</div>
		</div>
	</body>
	</html>';

?>