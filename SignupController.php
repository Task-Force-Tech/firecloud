<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use AppBundle\Entity\Location;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Payment;

class SignupController extends BaseController
{
	/**
	 * @Route("/signup", name="signup", options={"expose" = true})
	 */
	public function signupAction()
	{
		// Admins shouldn't be here
		if ($this->isAdmin()) {
			$this->addFlash('error', 'Please log out of your admin account to start the signup process.');
			return $this->redirectToRoute('admin_dashboard');
		}

		// Go to form if the user is already logged in
		if ($this->getCurrentUser()) {
			return $this->redirectToRoute('signup_form');
		}

		// TODO: implement user registration, ask for email address and password, create a user record
		die('this is going to be where they register.');
	}

	/**
	 * @Route("/signup/form", name="signup_form", options={"expose" = true})
	 */
	public function formAction(Request $request)
	{
		// User should only have one location at this point, so we're going to try to get it.
		// If there aren't any locations, we'll create one as part of this process.
		$user = $this->getCurrentUser();
		if (!$user) {
			return $this->redirectToRoute('signup');
		}

		// Admins shouldn't be here
		if ($this->isAdmin()) {
			$this->addFlash('error', 'Please log out of your admin account to start the signup process.');
			return $this->redirectToRoute('admin_dashboard');
		}

		$userLocations = $this->getDoctrine()->getRepository('AppBundle:Location')->findByUser($user);
		if (count($userLocations) === 0) {
			// create a new location
			$locationCreator = $this->get('app.location_creator');
			$location = $locationCreator->createLocation($user);
		} elseif (count($userLocations) === 1) {
			$location = $userLocations[0];

			// Once the signup is completed the user will edit via the admin-style
			// editor rather than the wizard form
			if ($location->getCompletedSignup()) {
				return $this->redirectToRoute('location', ['locationId' => $location->getId()]);
			}

		} else {
			throw new \Exception('User has too many locations. (Max: 1)');
		}

		$photoRepo = $this->getDoctrine()->getRepository('AppBundle:Photo');
		$photos = $photoRepo->findBy(array('location' => $location));

		$photosByCategory = array();
		foreach ($photos as $photo) {
			// Only admins will see certain categories
			if ($photo->getCategory() == Photo::CATEGORY_DOORS || $photo->getCategory() == Photo::CATEGORY_HYDRANTS) {
				continue;
			}
			$photosByCategory[$photo->getCategoryDescription()][] = $photo;
		}

		$paymentParams = [
			'amount_dollars'         => $this->container->getParameter('payment.amount_dollars'),
			'stripe_publishable_key' => $this->container->getParameter('payment.stripe_publishable_key')
		];

		return $this->render('user/signup_form.html.twig', array(
			'location'         => $location,
			'photosByCategory' => $photosByCategory,
			'paymentParams'    => $paymentParams
		));
	}

	/**
	 * @Route("/signup/form_complete", name="location_form_complete", options={"expose" = true})
	 */
	public function markLocationFormComplete(Request $request)
	{
		try {
			$this->rejectAdmin();

			$locationId = $request->request->get('locationId');
			if (!$locationId) {
				throw new \Exception('No location specified.');
			}
			$location = $this->getLocation($locationId);

			$location->setCompletedSignup(true);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return new JsonResponse(array('success' => true));

		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	/**
	 * @Route("/signup/pay", name="signup_pay", options={"expose" = true})
	 */
	public function payAction(Request $request)
	{
		\Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

		// Note: similar to LocationController->payAction

		try {
			$this->rejectAdmin();

			$token = $request->request->get('token');
			$charge = \Stripe\Charge::create(array(
				'amount'        => $this->container->getParameter('payment.amount_dollars') * 100,
				'currency'      => 'usd',
				'description'   => $this->container->getParameter('payment.description'),
				'source'        => $token,
				'receipt_email' => $request->request->get('email')
			));

			// I'm not sure what better thing we have to store as a transaction ID
			// in the database than the Charge Id.

			$locationId = $request->request->get('locationId');
			if (!$locationId) {
				throw new \Exception('No location specified.');
			}
			$location = $this->getLocation($locationId);

			$em = $this->getDoctrine()->getEntityManager();

			$user = $this->getCurrentUser();
			$now = new \DateTime();

			$payment = new Payment;
			$payment->setDate($now);
			$payment->setAmount($charge->amount); // cents
			$payment->setMethod(Payment::METHOD_CREDIT);
			$payment->setUser($user);
			$em->persist($payment);

			$location->setPayment($payment);
			$em->persist($location);

			$em->flush();

			$this->addFlash('success', 'Payment was processed successfully!');

			return new JsonResponse(array('success' => true));

		} catch (\Stripe\Error\Card $e) {
			// Since it's a decline, \Stripe\Error\Card will be caught
			$body = $e->getJsonBody();
			$err  = $body['error'];

			$error = sprintf('%s: %s', $err['code'], $err['message']);

			// print('Status is:' . $e->getHttpStatus() . '\n");
			// print('Type is:' . $err['type'] . "\n");
			// print('Code is:' . $err['code'] . "\n");
			// param is '' in this case
			// print('Param is:' . $err['param'] . "\n");
			// print('Message is:' . $err['message'] . "\n");
		} catch (\Stripe\Error\RateLimit $e) {
			// Too many requests made to the API too quickly
			$error = 'Too many requests. Please try again in a few minutes.';
		} catch (\Stripe\Error\InvalidRequest $e) {
			// Invalid parameters were supplied to Stripe's API
			$error = 'Invalid parameters. Please contact support.';
		} catch (\Stripe\Error\Authentication $e) {
			// Authentication with Stripe's API failed
			$error = 'Invalid API Key. Please contact support.';
		} catch (\Stripe\Error\ApiConnection $e) {
			// Network communication with Stripe failed
			$error = 'A network error occurred. Please try again in a few minutes.';
		} catch (\Stripe\Error\Base $e) {
			$error = 'An unknown error occurred.';
			// TODO: Should probably send ourselves an email at this point, and others.
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		return new JsonResponse(array('success' => false, 'error' => $error));
	}


	/**
	 * @Route("/signup/redeem_coupon", name="redeem_coupon", options={"expose" = true})
	 */
	public function redeemCouponAction(Request $request)
	{
		$code       = $request->request->get('code');
		$locationId = $request->request->get('locationId');
		$doctrine   = $this->getDoctrine();

		try {

			$user     = $this->getCurrentUser();
			$location = $this->getLocation($locationId);
			$now      = new \DateTime();

			if (!$code) {
				throw new \Exception('You must provide a code.');
			}

			$codeManager = $this->get('app.code_manager');
			$response = $codeManager->checkCode($code);
			if (!$response['success']) {
				throw new \Exception($response['error']);
			}

			$payment = new Payment;
			$payment->setDate($now);
			$payment->setAmount(0);
			$payment->setMethod(Payment::METHOD_PROMO);
			$payment->setUser($user);
			$payment->setPromoCode($code);

			$em = $doctrine->getEntityManager();
			$em->persist($payment);
			$em->flush();

			$location->setPayment($payment);
			$em->flush();

			$this->addFlash('success', 'Code was successfully applied.');

			return new JsonResponse(array('success' => true));

		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}

	}

}