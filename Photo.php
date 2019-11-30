<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="photo",
 *	indexes={
 *		@ORM\Index(name="category", columns={"category"})
 *	})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhotoRepository")
 */
class Photo
{
	const CATEGORY_SIDES    = 1;
	const CATEGORY_UTILITY  = 2;
	const CATEGORY_DOORS    = 3;
	const CATEGORY_HYDRANTS = 4;

	static public $categoryDescriptions = array(
		self::CATEGORY_SIDES    => 'Front / Back / Sides of Building',
		self::CATEGORY_UTILITY  => 'Gas / Electric / Water',
		self::CATEGORY_DOORS    => 'Exterior Doors',
		self::CATEGORY_HYDRANTS => 'Fire Hydrants'
	);

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Location", inversedBy="photos")
	 * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
	 */
	private $location;

	/**
	 * @ORM\Column(name="category", type="integer")
	 */
	private $category;

	/**
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;

	/**
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;

	/**
	 * @ORM\Column(name="file_location", type="string", length=255, nullable=true)
	 */
	private $fileLocation;

	/**
	 * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
	 */
	private $mimeType;

	/**
	 * @ORM\Column(name="size", type="integer", options={"default":0})
	 */
	private $size;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set category
	 *
	 * @param integer $category
	 *
	 * @return Photo
	 */
	public function setCategory($category)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * Get category
	 *
	 * @return integer
	 */
	public function getCategory()
	{
		return $this->category;
	}

	public function getCategoryDescription()
	{
		return self::$categoryDescriptions[$this->category];
	}

	/**
	 * Set fileLocation
	 *
	 * @param string $fileLocation
	 *
	 * @return Photo
	 */
	public function setFileLocation($fileLocation)
	{
		$this->fileLocation = $fileLocation;

		return $this;
	}

	/**
	 * Get fileLocation
	 *
	 * @return string
	 */
	public function getFileLocation()
	{
		return $this->fileLocation;
	}

	/**
	 * Set mimeType
	 *
	 * @param string $mimeType
	 *
	 * @return Photo
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;

		return $this;
	}

	/**
	 * Get mimeType
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * Set size
	 *
	 * @param integer $size
	 *
	 * @return Photo
	 */
	public function setSize($size)
	{
		$this->size = $size;

		return $this;
	}

	/**
	 * Get size
	 *
	 * @return integer
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Set location
	 *
	 * @param \AppBundle\Entity\Location $location
	 *
	 * @return Photo
	 */
	public function setLocation(\AppBundle\Entity\Location $location = null)
	{
		$this->location = $location;

		return $this;
	}

	/**
	 * Get location
	 *
	 * @return \AppBundle\Entity\Location
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Photo
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Photo
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
}
