<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Family
 *
 * @ORM\Table(name="family")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FamilyRepository")
 */
class Family
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
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="families")
     */
    private $map;

    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @var NurserySelection[]
     *
     * @ORM\OneToMany(targetEntity="NurserySelection", mappedBy="family", cascade={"persist", "remove"})
     */
    private $nurseries;

    /**
     * @param $n int
     * @return NurserySelection[]
     */
    public function getClosestNurserySelection($n)
    {
        $dist = -1;

        $result = [];

        $nurseries = $this->getNurseries()->toArray();

        usort($nurseries, function($a, $b) {
            if ($a->getDistance() < $b->getDistance()) return -1;
            return 1;
        });

        /** @var NurserySelection $n */
        foreach($nurseries as $nursery) {
            $result[] = $nursery;
            if (count($result) >= $n )
                break;
        }

        return $result;
    }

    public function __construct()
    {
        $this->nurseries = new ArrayCollection();
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
     * Set address
     *
     * @param \AppBundle\Entity\Address $address
     *
     * @return Family
     */
    public function setAddress(\AppBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \AppBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set map
     *
     * @param \AppBundle\Entity\Map $map
     *
     * @return Family
     */
    public function setMap(\AppBundle\Entity\Map $map = null)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return \AppBundle\Entity\Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set nursery
     *
     * @param \AppBundle\Entity\Nursery $nursery
     *
     * @return Family
     */
    public function setNursery(\AppBundle\Entity\Nursery $nursery = null)
    {
        $this->nursery = $nursery;

        return $this;
    }

    /**
     * Get nursery
     *
     * @return \AppBundle\Entity\Nursery
     */
    public function getNursery()
    {
        return $this->nursery;
    }

    /**
     * Set distance
     *
     * @param float $distance
     *
     * @return Family
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Add nurseries
     *
     * @param \AppBundle\Entity\NurserySelection $nurseries
     *
     * @return Family
     */
    public function addNurseries(\AppBundle\Entity\NurserySelection $nurseries)
    {
        $this->nurseries[] = $nurseries;

        return $this;
    }

    /**
     * Remove nurseries
     *
     * @param \AppBundle\Entity\NurserySelection $nurseries
     */
    public function removeNurseries(\AppBundle\Entity\NurserySelection $nurseries)
    {
        $this->nurseries->removeElement($nurseries);
    }

    /**
     * Get nurseries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNurseries()
    {
        return $this->nurseries;
    }
}
