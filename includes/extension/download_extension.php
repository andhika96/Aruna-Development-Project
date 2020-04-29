<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Source file from CodeIgniter v3.1.9
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 *	Information file: Download Extension
	 */

	defined('BASEPATH') OR exit('No direct script access allowed');

	// ------------------------------------------------------------------------

	if ( ! function_exists('force_download'))
	{
		/**
		 * Force Download
		 *
		 * Generates headers that force a download to happen
		 *
		 * @param	string	filename
		 * @param	mixed	the data to be downloaded
		 * @param	bool	whether to try and send the actual file MIME type
		 * @return	void
		 */

		function force_download($filename = '', $data = '', $set_mime = FALSE)
		{
			if ($filename === '' OR $data === '')
			{
				return;
			}
			elseif ($data === NULL)
			{
				if ( ! @is_file($filename) OR ($filesize = @filesize($filename)) === FALSE)
				{
					return;
				}

				$filepath = $filename;
				$filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
				$filename = end($filename);
			}
			else
			{
				$filesize = strlen($data);
			}

			// Set the default MIME type to send
			$mime = 'application/force-download';

			$x = explode('.', $filename);
			$extension = end($x);

			if ($set_mime === TRUE)
			{
				if (count($x) === 1 OR $extension === '')
				{
					/* 
					 * If we're going to detect the MIME type,
					 * we'll need a file extension.
					 */

					return;
				}

				// Load the mime types
				$mimes =& get_mimes();

				// Only change the default MIME if we can find one
				if (isset($mimes[$extension]))
				{
					$mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
				}
			}

			/* 
			 * It was reported that browsers on Android 2.1 (and possibly older as well)
			 * need to have the filename extension upper-cased in order to be able to
			 * download it.
			 *
			 * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
			 */

			if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT']))
			{
				$x[count($x) - 1] = strtoupper($extension);
				$filename = implode('.', $x);
			}

			if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE)
			{
				return;
			}

			// Clean output buffer
			if (ob_get_level() !== 0 && @ob_end_clean() === FALSE)
			{
				@ob_clean();
			}

			// Generate the server headers
			header('Content-Type: '.$mime);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.$filesize);
			header('Cache-Control: private, no-transform, no-store, must-revalidate');

			// If we have raw data - just dump it
			if ($data !== NULL)
			{
				exit($data);
			}

			$chunksize = 5 * (1024 * 1024); // 5 MB (= 5 242 880 bytes) per one chunk of file.

			// Flush 1MB chunks of data
			while ( ! feof($fp) && ($data = fread($fp, $chunksize)) !== FALSE)
			{
				echo $data;
			}

			fclose($fp);
			exit;
		}
	}

?>