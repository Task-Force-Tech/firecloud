<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location
 *
 * @ORM\Table(name="location",
 *	indexes={
 *		@ORM\Index(name="first_name", columns={"first_name"}),
 *		@ORM\Index(name="last_name", columns={"last_name"}),
 *		@ORM\Index(name="email", columns={"email"}),
 *		@ORM\Index(name="created_date", columns={"created_date"}),
 *		@ORM\Index(name="modified_date", columns={"modified_date"}),
 *		@ORM\Index(name="completed_signup", columns={"completed_signup"})
 *	})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationRepository")
 */
class Location
{
	const SECTION_CONTACT   = 'contact';
	const SECTION_ADDRESS   = 'address';
	const SECTION_PUBLIC    = 'public';
	const SECTION_PROPERTY  = 'property';
	const SECTION_OCCUPANTS = 'occupants';

	const FIELDS_BY_SECTION = [
		self::SECTION_CONTACT => [
			'firstName',
			'lastName',
			'email'
		],
		self::SECTION_ADDRESS => [
			'address',
			'address2',
			'city',
			'state',
			'zip'
		],
		self::SECTION_PUBLIC => [
			'squareFeet',
			'improvementType',
			'foundation',
			'yearBuilt',
			'quality',
			'interior',
			'exterior',
			'roofType',
			'roofCover'
		],
		self::SECTION_PROPERTY => [
			'homeType',
			'levels',
			'floor',
			'balconyAccess',
			'bedrooms',
			'basement',
			'solarPanels',
			'fireplace',
			'hazardousMaterials',
			'fireplace',
			'gatedCommunity',
			'gateCode'
		],
		self::SECTION_OCCUPANTS => [
			'medicalConcerns',
			'pets',
			'petsDetails'
		]
	];

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
	 */
	private $firstName;

	/**
	 * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
	 */
	private $lastName;

	/**
	 * @ORM\Column(name="email", type="string", length=255, nullable=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="address", type="string", length=255, nullable=true)
	 */
	private $address;

	/**
	 * @ORM\Column(name="address2", type="string", length=255, nullable=true)
	 */
	private $address2;

	/**
	 * @ORM\Column(name="city", type="string", length=255, nullable=true)
	 */
	private $city;

	/**
	 * @ORM\Column(name="state", type="string", length=255, nullable=true)
	 */
	private $state;

	/**
	 * @ORM\Column(name="zip", type="string", length=255, nullable=true)
	 */
	private $zip;

	/**
	 * @ORM\Column(name="created_date", type="datetime")
	 */
	private $createdDate;

	/**
	 * @ORM\Column(name="modified_date", type="datetime")
	 */
	private $modifiedDate;

	/**
	 * @ORM\Column(name="home_type", type="string", length=255, nullable=true)
	 */
	private $homeType;

	/**
	 * @ORM\Column(name="levels", type="integer", nullable=true)
	 */
	private $levels;

	/**
	 * @ORM\Column(name="floor", type="integer", nullable=true)
	 */
	private $floor;

	/**
	 * @ORM\Column(name="balcony_access", type="boolean", nullable=true)
	 */
	private $balconyAccess;

	/**
	 * @ORM\Column(name="bedrooms", type="integer", nullable=true)
	 */
	private $bedrooms;

	/**
	 * @ORM\Column(name="basement", type="boolean", nullable=true)
	 */
	private $basement;

	/**
	 * @ORM\Column(name="solar_panels", type="boolean", nullable=true)
	 */
	private $solarPanels;

	/**
	 * @ORM\Column(name="fireplace", type="boolean", nullable=true)
	 */
	private $fireplace;

	/**
	 * @ORM\Column(name="hazardous_materials", type="text", nullable=true)
	 */
	private $hazardousMaterials;

	/**
	 * @ORM\Column(name="gated_community", type="boolean", nullable=true)
	 */
	private $gatedCommunity;

	/**
	 * @ORM\Column(name="gate_code", type="string", length=255, nullable=true)
	 */
	private $gateCode;

	/**
	 * @ORM\Column(name="medical_concerns", type="text", nullable=true)
	 */
	private $medicalConcerns;

	/**
	 * @ORM\Column(name="pets", type="integer", nullable=true)
	 */
	private $pets;

	/**
	 * @ORM\Column(name="pets_details", type="text", nullable=true)
	 */
	private $petsDetails;

	/**
	 * @ORM\Column(name="square_feet", type="integer", nullable=true)
	 */
	private $squareFeet;

	/**
	 * @ORM\Column(name="improvement_type", type="string", length=255, nullable=true)
	 */
	private $improvementType;

	/**
	 * @ORM\Column(name="foundation", type="string", length=255, nullable=true)
	 */
	private $foundation;

	/**
	 * @ORM\Column(name="year_built", type="string", length=4, nullable=true)
	 */
	private $yearBuilt;

	/**
	 * @ORM\Column(name="quality", type="string", length=255, nullable=true)
	 */
	private $quality;

	/**
	 * @ORM\Column(name="interior", type="string", length=255, nullable=true)
	 */
	private $interior;

	/**
	 * @ORM\Column(name="exterior", type="string", length=255, nullable=true)
	 */
	private $exterior;

	/**
	 * @ORM\Column(name="roof_type", type="string", length=255, nullable=true)
	 */
	private $roofType;

	/**
	 * @ORM\Column(name="roof_cover", type="string", length=255, nullable=true)
	 */
	private $roofCover;

	/**
	 * @ORM\OneToMany(targetEntity="Photo", mappedBy="location")
	 */
	protected $photos;

	/**
	 * @ORM\Column(name="occupants", type="array", nullable=true)
	 */
	private $occupants;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="locations")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

	/**
	 * @ORM\Column(name="completed_signup", type="boolean", nullable=false, options={"default" : 0})
	 */
	private $completedSignup = 0;

	/**
	 * @ORM\OneToOne(targetEntity="Payment", mappedBy="location")
	 * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
	 */
	private $payment;


	/**
	 * @ORM\Column(type="boolean", nullable=false, options={"default" : 0})
	 */
	private $deleted;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setDeleted(false);
    }

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the first + last name
	 *
	 * @return  string
	 */
	public function getDisplayName()
	{
		$name = $this->getLastName();
		if ($this->getFirstName() && $this->getLastName()) {
			$name .= ', ';
		}
		$name .= $this->getFirstName();

		if (!$name) {
			if (!$this->getCompletedSignup()) {
				$name = '(signup not completed)';
			} else {
				$name = '(no name provided)';
			}
		}
		return $name;
	}

	/**
	 * Set firstName
	 *
	 * @param string $firstName
	 *
	 * @return Location
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * Get firstName
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * Set lastName
	 *
	 * @param string $lastName
	 *
	 * @return Location
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * Get lastName
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Location
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set address
	 *
	 * @param string $address
	 *
	 * @return Location
	 */
	public function setAddress($address)
	{
		$this->address = $address;

		return $this;
	}

	/**
	 * Get address
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Set address2
	 *
	 * @param string $address2
	 *
	 * @return Location
	 */
	public function setAddress2($address2)
	{
		$this->address2 = $address2;

		return $this;
	}

	/**
	 * Get address2
	 *
	 * @return string
	 */
	public function getAddress2()
	{
		return $this->address2;
	}

	/**
	 * Set city
	 *
	 * @param string $city
	 *
	 * @return Location
	 */
	public function setCity($city)
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * Get city
	 *
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * Set state
	 *
	 * @param string $state
	 *
	 * @return Location
	 */
	public function setState($state)
	{
		$this->state = $state;

		return $this;
	}

	/**
	 * Get state
	 *
	 * @return string
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Set zip
	 *
	 * @param string $zip
	 *
	 * @return Location
	 */
	public function setZip($zip)
	{
		$this->zip = $zip;

		return $this;
	}

	/**
	 * Get zip
	 *
	 * @return string
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * Get the address, city, state, zip
	 *
	 * @return string
	 */
	public function getDisplayAddress()
	{
		$a = $this->getAddress();
		if ($this->getAddress2()) {
			$a .= ' ' . $this->getAddress2() . ',';
		}
		if ($this->getCity()) {
			$a .= ' ' . $this->getCity();
		}
		if ($this->getState()) {
			if ($this->getCity()) {
				$a .= ', ';
			}
			$a .= $this->getState();
		}
		if ($this->getZip()) {
			$a .= ' ' . $this->getZip();
		}

		return $a;
	}

	/**
	 * Set createdDate
	 *
	 * @param \DateTime $createdDate
	 *
	 * @return Location
	 */
	public function setCreatedDate($createdDate)
	{
		$this->createdDate = $createdDate;

		return $this;
	}

	/**
	 * Get createdDate
	 *
	 * @return \DateTime
	 */
	public function getCreatedDate()
	{
		return $this->createdDate;
	}

	/**
	 * Set modifiedDate
	 *
	 * @param \DateTime $modifiedDate
	 *
	 * @return Location
	 */
	public function setModifiedDate($modifiedDate)
	{
		$this->modifiedDate = $modifiedDate;

		return $this;
	}

	/**
	 * Get modifiedDate
	 *
	 * @return \DateTime
	 */
	public function getModifiedDate()
	{
		return $this->modifiedDate;
	}

	/**
	 * Set homeType
	 *
	 * @param string $homeType
	 *
	 * @return Location
	 */
	public function setHomeType($homeType)
	{
		$this->homeType = $homeType;

		return $this;
	}

	/**
	 * Get homeType
	 *
	 * @return string
	 */
	public function getHomeType()
	{
		return $this->homeType;
	}

	/**
	 * Set levels
	 *
	 * @param integer $levels
	 *
	 * @return Location
	 */
	public function setLevels($levels)
	{
		$this->levels = $levels;

		return $this;
	}

	/**
	 * Get levels
	 *
	 * @return integer
	 */
	public function getLevels()
	{
		return $this->levels;
	}

	/**
	 * Set floor
	 *
	 * @param integer $floor
	 *
	 * @return Location
	 */
	public function setFloor($floor)
	{
		$this->floor = $floor;

		return $this;
	}

	/**
	 * Get floor
	 *
	 * @return integer
	 */
	public function getFloor()
	{
		return $this->floor;
	}

	/**
	 * Set balconyAccess
	 *
	 * @param boolean $balconyAccess
	 *
	 * @return Location
	 */
	public function setBalconyAccess($balconyAccess)
	{
		$this->balconyAccess = $balconyAccess;

		return $this;
	}

	/**
	 * Get balconyAccess
	 *
	 * @return boolean
	 */
	public function getBalconyAccess()
	{
		return $this->balconyAccess;
	}

	/**
	 * Set bedrooms
	 *
	 * @param integer $bedrooms
	 *
	 * @return Location
	 */
	public function setBedrooms($bedrooms)
	{
		$this->bedrooms = $bedrooms;

		return $this;
	}

	/**
	 * Get bedrooms
	 *
	 * @return integer
	 */
	public function getBedrooms()
	{
		return $this->bedrooms;
	}

	/**
	 * Set basement
	 *
	 * @param boolean $basement
	 *
	 * @return Location
	 */
	public function setBasement($basement)
	{
		$this->basement = $basement;

		return $this;
	}

	/**
	 * Get basement
	 *
	 * @return boolean
	 */
	public function getBasement()
	{
		return $this->basement;
	}

	/**
	 * Set solarPanels
	 *
	 * @param boolean $solarPanels
	 *
	 * @return Location
	 */
	public function setSolarPanels($solarPanels)
	{
		$this->solarPanels = $solarPanels;

		return $this;
	}

	/**
	 * Get solarPanels
	 *
	 * @return boolean
	 */
	public function getSolarPanels()
	{
		return $this->solarPanels;
	}

	/**
	 * Set fireplace
	 *
	 * @param boolean $fireplace
	 *
	 * @return Location
	 */
	public function setFireplace($fireplace)
	{
		$this->fireplace = $fireplace;

		return $this;
	}

	/**
	 * Get fireplace
	 *
	 * @return boolean
	 */
	public function getFireplace()
	{
		return $this->fireplace;
	}

	/**
	 * Set hazardousMaterials
	 *
	 * @param string $hazardousMaterials
	 *
	 * @return Location
	 */
	public function setHazardousMaterials($hazardousMaterials)
	{
		$this->hazardousMaterials = $hazardousMaterials;

		return $this;
	}

	/**
	 * Get hazardousMaterials
	 *
	 * @return string
	 */
	public function getHazardousMaterials()
	{
		return $this->hazardousMaterials;
	}

	/**
	 * Set gatedCommunity
	 *
	 * @param boolean $gatedCommunity
	 *
	 * @return Location
	 */
	public function setGatedCommunity($gatedCommunity)
	{
		$this->gatedCommunity = $gatedCommunity;

		return $this;
	}

	/**
	 * Get gatedCommunity
	 *
	 * @return boolean
	 */
	public function getGatedCommunity()
	{
		return $this->gatedCommunity;
	}

	/**
	 * Set gateCode
	 *
	 * @param string $gateCode
	 *
	 * @return Location
	 */
	public function setGateCode($gateCode)
	{
		$this->gateCode = $gateCode;

		return $this;
	}

	/**
	 * Get gateCode
	 *
	 * @return string
	 */
	public function getGateCode()
	{
		return $this->gateCode;
	}

	/**
	 * Set medicalConcerns
	 *
	 * @param string $medicalConcerns
	 *
	 * @return Location
	 */
	public function setMedicalConcerns($medicalConcerns)
	{
		$this->medicalConcerns = $medicalConcerns;

		return $this;
	}

	/**
	 * Get medicalConcerns
	 *
	 * @return string
	 */
	public function getMedicalConcerns()
	{
		return $this->medicalConcerns;
	}

	/**
	 * Set pets
	 *
	 * @param boolean $pets
	 *
	 * @return Location
	 */
	public function setPets($pets)
	{
		$this->pets = $pets;

		return $this;
	}

	/**
	 * Get pets
	 *
	 * @return boolean
	 */
	public function getPets()
	{
		return $this->pets;
	}

	/**
	 * Set petsDetails
	 *
	 * @param string $petsDetails
	 *
	 * @return Location
	 */
	public function setPetsDetails($petsDetails)
	{
		$this->petsDetails = $petsDetails;

		return $this;
	}

	/**
	 * Get petsDetails
	 *
	 * @return string
	 */
	public function getPetsDetails()
	{
		return $this->petsDetails;
	}

	/**
	 * Set squareFeet
	 *
	 * @param integer $squareFeet
	 *
	 * @return Location
	 */
	public function setSquareFeet($squareFeet)
	{
		$this->squareFeet = $squareFeet;

		return $this;
	}

	/**
	 * Get squareFeet
	 *
	 * @return integer
	 */
	public function getSquareFeet()
	{
		return $this->squareFeet;
	}

	/**
	 * Set improvementType
	 *
	 * @param string $improvementType
	 *
	 * @return Location
	 */
	public function setImprovementType($improvementType)
	{
		$this->improvementType = $improvementType;

		return $this;
	}

	/**
	 * Get improvementType
	 *
	 * @return string
	 */
	public function getImprovementType()
	{
		return $this->improvementType;
	}

	/**
	 * Set foundation
	 *
	 * @param string $foundation
	 *
	 * @return Location
	 */
	public function setFoundation($foundation)
	{
		$this->foundation = $foundation;

		return $this;
	}

	/**
	 * Get foundation
	 *
	 * @return string
	 */
	public function getFoundation()
	{
		return $this->foundation;
	}

	/**
	 * Set yearBuilt
	 *
	 * @param string $yearBuilt
	 *
	 * @return Location
	 */
	public function setYearBuilt($yearBuilt)
	{
		$this->yearBuilt = $yearBuilt;

		return $this;
	}

	/**
	 * Get yearBuilt
	 *
	 * @return string
	 */
	public function getYearBuilt()
	{
		return $this->yearBuilt;
	}

	/**
	 * Set quality
	 *
	 * @param string $quality
	 *
	 * @return Location
	 */
	public function setQuality($quality)
	{
		$this->quality = $quality;

		return $this;
	}

	/**
	 * Get quality
	 *
	 * @return string
	 */
	public function getQuality()
	{
		return $this->quality;
	}

	/**
	 * Set interior
	 *
	 * @param string $interior
	 *
	 * @return Location
	 */
	public function setInterior($interior)
	{
		$this->interior = $interior;

		return $this;
	}

	/**
	 * Get interior
	 *
	 * @return string
	 */
	public function getInterior()
	{
		return $this->interior;
	}

	/**
	 * Set exterior
	 *
	 * @param string $exterior
	 *
	 * @return Location
	 */
	public function setExterior($exterior)
	{
		$this->exterior = $exterior;

		return $this;
	}

	/**
	 * Get exterior
	 *
	 * @return string
	 */
	public function getExterior()
	{
		return $this->exterior;
	}

	/**
	 * Set roofType
	 *
	 * @param string $roofType
	 *
	 * @return Location
	 */
	public function setRoofType($roofType)
	{
		$this->roofType = $roofType;

		return $this;
	}

	/**
	 * Get roofType
	 *
	 * @return string
	 */
	public function getRoofType()
	{
		return $this->roofType;
	}

	/**
	 * Set roofCover
	 *
	 * @param string $roofCover
	 *
	 * @return Location
	 */
	public function setRoofCover($roofCover)
	{
		$this->roofCover = $roofCover;

		return $this;
	}

	/**
	 * Get roofCover
	 *
	 * @return string
	 */
	public function getRoofCover()
	{
		return $this->roofCover;
	}

	/**
	 * Returns true if any of the public section fields have been filled in
	 *
	 * @return boolean
	 */
	public function hasPublicSectionData()
	{
		return $this->hasSectionData(self::SECTION_PUBLIC);
	}

	/**
	 * Returns true if any of the property section fields have been filled in
	 *
	 * @return boolean
	 */
	public function hasPropertySectionData()
	{
		return $this->hasSectionData(self::SECTION_PROPERTY);
	}

	/**
	 * Returns true if any of the specified section fields have been filled in
	 *
	 * @return boolean
	 */
	protected function hasSectionData($section)
	{
		foreach (self::FIELDS_BY_SECTION[$section] as $field) {
			$func = 'get' . ucfirst($field);
			if (strlen($this->$func()) > 0) {
				return true;
			}
		}
		return false;
	}

    /**
     * Add photo
     *
     * @param \AppBundle\Entity\Photo $photo
     *
     * @return Location
     */
    public function addPhoto(\AppBundle\Entity\Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \AppBundle\Entity\Photo $photo
     */
    public function removePhoto(\AppBundle\Entity\Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set occupants
     *
     * @param array $occupants
     *
     * @return Location
     */
    public function setOccupants($occupants)
    {
        $this->occupants = $occupants;

        return $this;
    }

    /**
     * Get occupants
     *
     * @return array
     */
    public function getOccupants()
    {
        return $this->occupants;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Location
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set completedSignup
     *
     * @param boolean $completedSignup
     *
     * @return Location
     */
    public function setCompletedSignup($completedSignup)
    {
        $this->completedSignup = $completedSignup;

        return $this;
    }

    /**
     * Get completedSignup
     *
     * @return boolean
     */
    public function getCompletedSignup()
    {
        return $this->completedSignup;
    }

    /**
     * Set payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return Location
     */
    public function setPayment(\AppBundle\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \AppBundle\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    public function hasPayment()
    {
    	return ($this->payment);
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Location
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    public function isDeleted()
    {
    	return $this->deleted;
    }
}
