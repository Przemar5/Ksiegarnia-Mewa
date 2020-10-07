<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorPageRenderer
{
	/**
	 * Error code
	 */
	private $code;

	public function __construct($code)
	{
		$this->code = $code;
	}

	public function render()
	{
		
	}
}