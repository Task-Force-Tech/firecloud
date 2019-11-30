<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Location;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Photo;

class LocationController extends BaseController
{
	// Calls setX on the a Location record for the given fields, pulling the data from POST
	protected function setSectionFieldsOnLocation(Location $location, array $fields, Request $request)
	{
		foreach ($fields as $field) {
			$setter = 'set' . ucfirst($field);
			$value = $request->request->get($field);
			if (strlen($value) == 0) {
				$value = null;
			}
			$location->$setter($value);
		}
	}

	/**
	 * @Route("/location/new", name="location_new", options={"expose" = true})
	 */
	public function newAction(Request $request)
	{
		$this->requireAdmin();

		if ($request->isMethod('POST')) {
			$locationCreator = $this->get('app.location_creator');
			$location = $locationCreator->createLocation();

			$fields = Location::FIELDS_BY_SECTION[Location::SECTION_CONTACT];
			$this->setSectionFieldsOnLocation($location, $fields, $request);

			$fields = Location::FIELDS_BY_SECTION[Location::SECTION_ADDRESS];
			$this->setSectionFieldsOnLocation($location, $fields, $request);

			$em = $this->getDoctrine()->getManager();
			$em->persist($location);
			$em->flush();

			return $this->redirectToRoute('location', array('locationId' => $location->getId()));
		} else {
			throw new \Exception('Invalid usage.');
		}
	}

	/**
	 * @Route("/location/{locationId}", name="location", requirements={"locationId" = "\d+"}, options={"expose" = true})
	 */
	public function viewAction(Request $request, $locationId)
	{
		$user = $this->getCurrentUser();
		$location = $this->getLocation($locationId);

		$photoRepo = $this->getDoctrine()->getRepository('AppBundle:Photo');
		$photos = $photoRepo->findBy(array('location' => $location));

		$photosByCategory = array();
		foreach ($photos as $photo) {
			if (!$this->isAdmin() && ($photo->getCategory() == Photo::CATEGORY_DOORS || $photo->getCategory() == Photo::CATEGORY_HYDRANTS)) {
				continue;
			}
			$photosByCategory[$photo->getCategoryDescription()][] = $photo;
		}

		$paymentParams = [
			'amount_dollars'         => $this->container->getParameter('payment.amount_dollars'),
			'name'                   => $this->container->getParameter('payment.name'),
			'description'            => $this->container->getParameter('payment.description'),
			'stripe_publishable_key' => $this->container->getParameter('payment.stripe_publishable_key')
		];

		return $this->render('admin/location.html.twig', [
			'location'         => $location,
			'photosByCategory' => $photosByCategory,
			'paymentParams'    => $paymentParams
		]);
	}

	/**
	 * @Route("/location/{locationId}/edit/{section}", name="location_edit_section", options={"expose" = true})
	 */
	public function editSection(Request $request, $locationId, $section)
	{
		try {
			$location = $this->getLocation($locationId);
			if (!isset(Location::FIELDS_BY_SECTION[$section])) {
				throw new \Exception('Invalid section');
			}

			if ($request->isMethod('POST')) {
				// Because we have to do special stuff for occupants, I'm doing this.
				// I don't like it. -Jay
				if ($section !== Location::SECTION_OCCUPANTS) {
					$fields = Location::FIELDS_BY_SECTION[$section];
					$this->setSectionFieldsOnLocation($location, $fields, $request);

					$location->setModifiedDate(new \Datetime);
				} else {
					$formData = $request->request->get('formData');
					$jsonData = json_decode($formData, true);
					$occupants = $jsonData['occupants'];
					$location->setOccupants($occupants);
					$location->setMedicalConcerns($jsonData['fields']['medicalConcerns']);
					$location->setPets($jsonData['fields']['pets']);
					$location->setPetsDetails($jsonData['fields']['petsDetails']);
				}
				$em = $this->getDoctrine()->getManager();
				$em->persist($location);
				$em->flush();

				return new JsonResponse(['success' => true]);
			}

			$template = sprintf('forms/%s.html.twig', $section);
			$html = $this->renderView($template, [
				'location' => $location
			]);

			return new JsonResponse(array('success' => true, 'html' => $html));
		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	/**
	 * @Route("/location/{locationId}/delete", name="location_delete", options={"expose" = true})
	 */
	public function deleteAction($locationId)
	{
		try {
			$this->requireAdmin();
			$location = $this->getLocation($locationId);
			$location->setDeleted(true);
			$this->getDoctrine()->getEntityManager()->flush();
			return new JsonResponse(array('success' => true));
		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $error));
		}
	}

	/**
	 * @Route("/photo/{photoId}/edit", name="photo_modal", requirements={"photoId" = "\d+"}, options={"expose" = true})
	 */
	public function getPhotoModalAction(Request $request, $photoId)
	{
		try {
			$photo = $this->getDoctrine()->getRepository('AppBundle:Photo')->find($photoId);
			if (!$photo) {
				throw new \Exception('Could not find photo.');
			}

			// Roundabout way to make sure user can access this record
			$location = $this->getLocation($photo->getLocation()->getId());

			$form = $this->createFormBuilder($photo)
				->add('name', TextType::class)
				->add('description', TextareaType::class)
				->getForm();

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$this->getDoctrine()->getManager()->flush();
				return new JsonResponse(array('success' => true, 'url' => $photo->getFileLocation()));
			}

			$html = $this->renderView('admin/photo_modal.html.twig', array('photo' => $photo, 'form' => $form->createView()));

			return new JsonResponse(array('success' => true, 'html' => $html));

		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	/**
	 * @Route("/image/upload", name="location_image_upload", options={"expose" = true})
	 */
	public function uploadImage(Request $request)
	{
		try {
			$file = $request->files->get('file');
			if ($file === null) {
				throw new \Exception('An unknown error occurred.');
			}

			$id = $request->request->get('id');
			if (!$id) {
				throw new \Exception('Invalid image ID.');
			}

			$photo = $this->getDoctrine()->getRepository('AppBundle:Photo')->find($id);
			if (!$photo) {
				throw new \Exception('Could not find photo.');
			}

			// Roundabout way to make sure user can access this record
			$location = $this->getLocation($photo->getLocation()->getId());

			$prevPhoto = $photo->getFileLocation();

			$imageUploader = $this->get('app.image_uploader');

			$fileInfo = $imageUploader->processUploadedImage($file, 800, 800);
			$webPath = $fileInfo['webPath'];

			$photo->setFileLocation($webPath);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			// If everything was successful, we want to remove the previous file, if there
			// was one.
			if ($prevPhoto) {
				// TODO: THIS WORKS, but I'm a little worried about how it could fail to work.
				// We should make this more bulletproof in the future when we have more time.
				unlink(getcwd() . $prevPhoto);
			}

			return new JsonResponse(array('success' => true, 'url' => $webPath));

		} catch (\Exception $e) {
			return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	/**
	 * @Route("/location/{locationId}/pay", name="location_pay")
	 */
	public function payAction($locationId, Request $request)
	{
		\Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

		$location = $this->getLocation($locationId);
		if ($location->getPayment()) {
			$this->addFlash('error', 'A payment has already been applied to this location.');
			return $this->redirectToRoute('location', ['locationId' => $locationId]);
		}

		// Note: similar to SignupController->payAction

		try {
			$token = $request->request->get('stripeToken');
			$charge = \Stripe\Charge::create(array(
				'amount'        => $this->container->getParameter('payment.amount_dollars') * 100,
				'currency'      => 'usd',
				'description'   => $this->container->getParameter('payment.description'),
				'source'        => $token,
				'receipt_email' => $request->request->get('stripeEmail')
			));

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

			$this->addFlash('success', 'Payment processed successfully.');
			return $this->redirectToRoute('location', ['locationId' => $locationId]);

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

		$this->addFlash('error', $error);
		return $this->redirectToRoute('location', ['locationId' => $locationId]);
	}
}
