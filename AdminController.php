<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends BaseController
{
	/**
	 * @Route("/admin")
	 * @Route("/admin/dashboard", name="admin_dashboard", options={"expose" = true})
	 */
	public function dashboardAction(Request $request)
	{
		$this->requireAdmin();

		$locations = $this->getDoctrine()->getRepository('AppBundle:Location')
			->findBy(['deleted' => false], ['lastName' => 'ASC', 'firstName' => 'ASC']);

		return $this->render('admin/dashboard.html.twig', [
			'locations' => $locations
		]);
	}


	/**
	 * @Route("/admin/codes/{numCodes}", name="admin_codes")
	 */
	public function getCodeAction($numCodes = 1)
	{
		$user = $this->getCurrentUser();
		if (!$user->isSuperAdmin()) {
			throw new \Exception('Permission denied');
		}

		$codeManager = $this->get('app.code_manager');
		$codes = $codeManager->generateCodes($numCodes, 3);
		foreach ($codes as $code) {
			echo $code . '<br />';
		}
		exit;
	}

	/**
	 * @Route("/admin/codes/check/{code}", name="admin_codes_check", options={"expose" = true})
	 */
	public function checkCodeAction($code)
	{
		$user = $this->getCurrentUser();
		if (!$user->isSuperAdmin()) {
			throw new \Exception('Permission denied');
		}

		$codeManager = $this->get('app.code_manager');
		$response = $codeManager->checkCode($code);
		return new JsonResponse($response);
	}
}