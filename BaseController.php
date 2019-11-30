<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BaseController extends Controller
{
	protected function getCurrentUser()
	{
		$token = $this->get('security.token_storage')->getToken();
		return ($token->isAuthenticated()) ? $token->getUser() : null;
	}

	protected function isAdmin()
	{
		$user = $this->getCurrentUser();
		return is_object($user) && $user->hasRole('ROLE_ADMIN');
	}

	protected function requireAdmin()
	{
		if (!$this->isAdmin()) {
			throw new AccessDeniedException;
		}
	}

	protected function rejectAdmin($message = null)
	{
		if ($this->isAdmin()) {
			$message = ($message) ?: 'This action is not available to admin users.';
			throw new \Exception($message);
		}
	}

	// Loads a location and makes sure the current user can access it
	protected function getLocation($locationId)
	{
		$location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($locationId);
		if (!$location) {
			throw new \Exception('Location does not exist.');
		}

		// We could also do this when we're finding the location, but this will make more
		// clear what's happening instead of just saying we can't find it.
		if ($location->isDeleted()) {
			throw new \Exception('Location has been deleted.');
		}

		if ($location->getUser() !== $this->getCurrentUser() && !$this->isAdmin()) {
			throw new AccessDeniedException;
		}

		return $location;
	}
}
