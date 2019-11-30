<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction(Request $request)
	{
		if ($this->isAdmin()) {
			return $this->redirectToRoute('admin_dashboard');
		} else {
			$user = $this->getCurrentUser();
			$userLocations = $this->getDoctrine()->getRepository('AppBundle:Location')->findByUser($user);
			if (count($userLocations) === 1) {
				$location = $userLocations[0];
				if ($location->getCompletedSignup()) {
					return $this->redirectToRoute('location', array('locationId' => $location->getId()));
				} else {
					return $this->redirectToRoute('signup_form');
				}

			} elseif (count($userLocations) === 0) {
				return $this->redirectToRoute('signup_form');
			} else {
				// they shouldn't have more than one location yet since we aren't providing a way
				// for it, but this is where we'd redirect them to a page where they can view their
				// different locations.
				die('Error: multiple locations for a user is not yet supported');
			}
		}
	}
}
