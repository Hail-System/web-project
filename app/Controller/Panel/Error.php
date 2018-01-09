<?php

namespace App\Controller\Panel;

use Hail\Controller;
use Hail\Exception\ActionError;

class Error extends Controller
{
	public function indexAction()
	{
        /** @var ActionError $error */
		$error = $this->app->param('error');

		return [
			'ret' => $error->getCode(),
			'msg' => $error->getMessage(),
		];
	}
}