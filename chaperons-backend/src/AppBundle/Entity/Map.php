<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Map
 *
 * @ORM\Table(name="map")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MapRepository")
 */
class Map
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showMicro = true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showMac = true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showPartners = true;

    /**
     * @SerializedName("show_dsp")
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showDSP = true;

    /**
     * @SerializedName("show_dspc")
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showDSPC = true;

    /**
     * @SerializedName("show_other")
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showOther = true;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $neLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $neLng;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $swLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $swLng;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $zoom;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $centerLat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $centerLng;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $fillColorFamily = '#44e318';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $fillColorNursery = '#1855e3';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $fillColorNurseryOwned = '#e31c18';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $styleName;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $nurseriesByFamily = 30;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $nurseriesMaxDistance = 30000;


    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $captureFilename;

    /**
     * @Exclude()
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="maps")
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @Groups({"detail"})
     *
     * @ORM\OneToMany(targetEntity="Family", mappedBy="map", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $families;

    public function __construct()
    {
        $this->families = new ArrayCollection();
    }


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
     * Set name
     *
     * @param string $name
     *
     * @return Map
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Map
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set showMicro
     *
     * @param boolean $showMicro
     *
     * @return Map
     */
    public function setShowMicro($showMicro)
    {
        $this->showMicro = $showMicro;

        return $this;
    }

    /**
     * Get showMicro
     *
     * @return boolean
     */
    public function getShowMicro()
    {
        return $this->showMicro;
    }

    /**
     * Set showMac
     *
     * @param boolean $showMac
     *
     * @return Map
     */
    public function setShowMac($showMac)
    {
        $this->showMac = $showMac;

        return $this;
    }

    /**
     * Get showMac
     *
     * @return boolean
     */
    public function getShowMac()
    {
        return $this->showMac;
    }

    /**
     * Set showPartners
     *
     * @param boolean $showPartners
     *
     * @return Map
     */
    public function setShowPartners($showPartners)
    {
        $this->showPartners = $showPartners;

        return $this;
    }

    /**
     * Get showPartners
     *
     * @return boolean
     */
    public function getShowPartners()
    {
        return $this->showPartners;
    }

    /**
     * Set showDSP
     *
     * @param boolean $showDSP
     *
     * @return Map
     */
    public function setShowDSP($showDSP)
    {
        $this->showDSP = $showDSP;

        return $this;
    }

    /**
     * Get showDSP
     *
     * @return boolean
     */
    public function getShowDSP()
    {
        return $this->showDSP;
    }

    /**
     * Set neLat
     *
     * @param float $neLat
     *
     * @return Map
     */
    public function setNeLat($neLat)
    {
        $this->neLat = $neLat;

        return $this;
    }

    /**
     * Get neLat
     *
     * @return float
     */
    public function getNeLat()
    {
        return $this->neLat;
    }

    /**
     * Set neLng
     *
     * @param float $neLng
     *
     * @return Map
     */
    public function setNeLng($neLng)
    {
        $this->neLng = $neLng;

        return $this;
    }

    /**
     * Get neLng
     *
     * @return float
     */
    public function getNeLng()
    {
        return $this->neLng;
    }

    /**
     * Set swLat
     *
     * @param float $swLat
     *
     * @return Map
     */
    public function setSwLat($swLat)
    {
        $this->swLat = $swLat;

        return $this;
    }

    /**
     * Get swLat
     *
     * @return float
     */
    public function getSwLat()
    {
        return $this->swLat;
    }

    /**
     * Set swLng
     *
     * @param float $swLng
     *
     * @return Map
     */
    public function setSwLng($swLng)
    {
        $this->swLng = $swLng;

        return $this;
    }

    /**
     * Get swLng
     *
     * @return float
     */
    public function getSwLng()
    {
        return $this->swLng;
    }

    /**
     * Set fillColorFamily
     *
     * @param string $fillColorFamily
     *
     * @return Map
     */
    public function setFillColorFamily($fillColorFamily)
    {
        $this->fillColorFamily = $fillColorFamily;

        return $this;
    }

    /**
     * Get fillColorFamily
     *
     * @return string
     */
    public function getFillColorFamily()
    {
        return $this->fillColorFamily;
    }

    /**
     * Set fillColorNursery
     *
     * @param string $fillColorNursery
     *
     * @return Map
     */
    public function setFillColorNursery($fillColorNursery)
    {
        $this->fillColorNursery = $fillColorNursery;

        return $this;
    }

    /**
     * Get fillColorNursery
     *
     * @return string
     */
    public function getFillColorNursery()
    {
        return $this->fillColorNursery;
    }

    /**
     * Set nurseriesByFamily
     *
     * @param integer $nurseriesByFamily
     *
     * @return Map
     */
    public function setNurseriesByFamily($nurseriesByFamily)
    {
        $this->nurseriesByFamily = $nurseriesByFamily;

        return $this;
    }

    /**
     * Get nurseriesByFamily
     *
     * @return integer
     */
    public function getNurseriesByFamily()
    {
        return $this->nurseriesByFamily;
    }

    /**
     * Set nurseriesMaxDistance
     *
     * @param integer $nurseriesMaxDistance
     *
     * @return Map
     */
    public function setNurseriesMaxDistance($nurseriesMaxDistance)
    {
        $this->nurseriesMaxDistance = $nurseriesMaxDistance;

        return $this;
    }

    /**
     * Get nurseriesMaxDistance
     *
     * @return integer
     */
    public function getNurseriesMaxDistance()
    {
        return $this->nurseriesMaxDistance;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Map
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set captureFilename
     *
     * @param string $captureFilename
     *
     * @return Map
     */
    public function setCaptureFilename($captureFilename)
    {
        $this->captureFilename = $captureFilename;

        return $this;
    }

    /**
     * Get captureFilename
     *
     * @return string
     */
    public function getCaptureFilename()
    {
        return $this->captureFilename;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Map
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
     * Add family
     *
     * @param \AppBundle\Entity\Family $family
     *
     * @return Map
     */
    public function addFamily(\AppBundle\Entity\Family $family)
    {
        $this->families[] = $family;

        return $this;
    }

    /**
     * Remove family
     *
     * @param \AppBundle\Entity\Family $family
     */
    public function removeFamily(\AppBundle\Entity\Family $family)
    {
        $this->families->removeElement($family);
    }

    /**
     * Get families
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFamilies()
    {
        return $this->families;
    }

    /**
     * Set styleName
     *
     * @param string $styleName
     *
     * @return Map
     */
    public function setStyleName($styleName)
    {
        $this->styleName = $styleName;

        return $this;
    }

    /**
     * Get styleName
     *
     * @return string
     */
    public function getStyleName()
    {
        return $this->styleName;
    }

    /**
     * Set zoom
     *
     * @param float $zoom
     *
     * @return Map
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;

        return $this;
    }

    /**
     * Get zoom
     *
     * @return float
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * Set centerLat
     *
     * @param float $centerLat
     *
     * @return Map
     */
    public function setCenterLat($centerLat)
    {
        $this->centerLat = $centerLat;

        return $this;
    }

    /**
     * Get centerLat
     *
     * @return float
     */
    public function getCenterLat()
    {
        return $this->centerLat;
    }

    /**
     * Set centerLng
     *
     * @param float $centerLng
     *
     * @return Map
     */
    public function setCenterLng($centerLng)
    {
        $this->centerLng = $centerLng;

        return $this;
    }

    /**
     * Get centerLng
     *
     * @return float
     */
    public function getCenterLng()
    {
        return $this->centerLng;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Map
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Map
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getFillColorNurseryOwned()
    {
        return $this->fillColorNurseryOwned;
    }

    /**
     * @param string $fillColorNurseryOwned
     */
    public function setFillColorNurseryOwned($fillColorNurseryOwned)
    {
        $this->fillColorNurseryOwned = $fillColorNurseryOwned;
    }

    /**
     * @return bool
     */
    public function getShowDSPC()
    {
        return $this->showDSPC;
    }

    /**
     * @param bool $showDSPC
     */
    public function setShowDSPC($showDSPC)
    {
        $this->showDSPC = $showDSPC;
    }

    /**
     * @return bool
     */
    public function getShowOther()
    {
        return $this->showOther;
    }

    /**
     * @param bool $showOther
     */
    public function setShowOther($showOther)
    {
        $this->showOther = $showOther;
    }


}
