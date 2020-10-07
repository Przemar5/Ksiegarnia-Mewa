<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
	/**
	 * Map - error code => method
	 */
	private $statusCodeMethodMap = [
		404 => 'renderPageNotFound',
	];

    public function show(Request $request, $code = 404)
    {
    	$method = $this->statusCodeMethodMap[
    		$code ?? $request->get('exception')->getStatusCode()
    	];

    	return $this->$method();
    }

    public function renderPageNotFound()
    {
    	return $this->render('error/page_not_found.html.twig');
    }
}
