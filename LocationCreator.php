<?php
namespace AppBundle\Service;

use \AppBundle\Entity\Location;
use \AppBundle\Entity\Photo;
use \AppBundle\Entity\User;

class LocationCreator {

	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function createLocation(User $user = null)
	{
		$date = new \DateTime();

		$location = new Location();
		$location->setCreatedDate($date);
		$location->setModifiedDate($date);
		$location->setDeleted(false);

		if ($user) {
			$location->setUser($user);
			if ($user->hasValidEmailFormat()) {
				$location->setEmail($user->getEmail());
			}
		}

		$em = $this->doctrine->getManager();
		$em->persist($location);
		$em->flush();

		$this->generateLocationPhotos($location);

		return $location;
	}

	private function generateLocationPhotos(Location $location)
	{
		$em = $this->doctrine->getManager();

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_SIDES);
		$photo->setName('Front');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_SIDES);
		$photo->setName('Back');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_SIDES);
		$photo->setName('Left');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_SIDES);
		$photo->setName('Right');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_UTILITY);
		$photo->setName('Furnace');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_UTILITY);
		$photo->setName('Water Heater');
		$em->persist($photo);

		$photo = new Photo;
		$photo->setLocation($location);
		$photo->setSize(0);
		$photo->setCategory(Photo::CATEGORY_UTILITY);
		$photo->setName('Utilities');
		$em->persist($photo);

		for ($i = 1; $i <= 6; $i++) {
			$photo = new Photo;
			$photo->setLocation($location);
			$photo->setSize(0);
			$photo->setCategory(Photo::CATEGORY_DOORS);
			$photo->setName('Door ' . $i);
			$em->persist($photo);
		}

		for ($i = 1; $i <= 3; $i++) {
			$photo = new Photo;
			$photo->setLocation($location);
			$photo->setSize(0);
			$photo->setCategory(Photo::CATEGORY_HYDRANTS);
			$photo->setName('Hydrant ' . $i);
			$em->persist($photo);
		}

		$em->flush();
	}
}