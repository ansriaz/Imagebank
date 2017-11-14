<?php

namespace App\Logic\Services;

use Log;

class CoreServices
{

	public function makeFolder ($path)
	{
		if (!file_exists($path))
		{
			mkdir($path, 0777, true);
		}
		return $path;
	}

}