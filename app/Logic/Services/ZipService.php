<?php

namespace App\Logic\Services;

// Require Composer's autoloader
require dirname(__DIR__) . '../../../vendor/autoload.php';

// use Alchemy\Zippy\Zippy;
// "alchemy/zippy": "^0.3.5", // composer.json

use ZipStream;

class ZipService
{

	public function createZipArchive($file_names,$archive_file_name,$file_path)
	{
		// 	$zip = new \ZipArchive();
		// //create the file and throw the error if unsuccessful
		// 	if ($zip->open($archive_file_name, \ZipArchive::CREATE )!==TRUE) {
		// 		exit("cannot open <$archive_file_name>\n");
		// 	}
		// //add each files of $file_name array to archive
		// 	foreach($file_names as $files)  {
		// 		$zip->addFile($file_path.$files,$files);
		// 	}
		// 	$zip->close();
		// exit;

		// Load Zippy
		// $zippy = Zippy::load();

		// creates
		// $archiveZip = $zippy->create($archive_file_name);

		// updates
		// $archiveZip->addMembers($file_names,
		//     $recursive = false
		// );


		# create a new zipstream object
		$zip = new ZipStream\ZipStream($archive_file_name);
		
		// create a file named 'hello.txt'
		foreach($file_names as $files)
		{
		// $zip->addFile($file_path.$files,$files);
			$zip->addFileFromPath($files, $file_path.'/'.$files);
		}

		# finish the zip stream
		$zip->finish();
	}

}