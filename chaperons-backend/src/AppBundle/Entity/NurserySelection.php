<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NurserySelection
 *
 * @ORM\Table(name="nursery_selection")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NurserySelectionRepository")
 */
class NurserySelection
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
     * @var float
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var Nursery
     *
     * @ORM\ManyToOne(targetEntity="Nursery")
     */
    private $nursery;

    /**
     * @var Family
     *
     * @ORM\ManyToOne(targetEntity="Family")
     */
    private $family;

    public function getFormattedDistance()
    {
        if ($this->distance < 1000) return round($this->distance) . 'm';
        else return number_format($this->distance/1000., 1, '.', ' ') . 'km';
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
     * Set distance
     *
     * @param float $distance
     *
     * @return NurserySelection
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
     * Set nursery
     *
     * @param \AppBundle\Entity\Nursery $nursery
     *
     * @return NurserySelection
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
     * Set family
     *
     * @param \AppBundle\Entity\Family $family
     *
     * @return NurserySelection
     */
    public function setFamily(\AppBundle\Entity\Family $family = null)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return \AppBundle\Entity\Family
     */
    public function getFamily()
    {
        return $this->family;
    }
}
