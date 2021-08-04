<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Nursery
 *
 * @ORM\Table(name="nursery")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NurseryRepository")
 */
class Nursery
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
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $source_id;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $nature;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $type;

    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="NurserySelection", mappedBy="nursery", cascade={"remove"})
     * @Serializer\Exclude()
     */
    private $selections;

    public function __construct()
    {
        $this->selections = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Nursery
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
     * Get address
     *
     * @return \AppBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set nature
     *
     * @param string $nature
     *
     * @return Nursery
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Nursery
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set address
     *
     * @param \AppBundle\Entity\Address $address
     *
     * @return Nursery
     */
    public function setAddress(\AppBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Set sourceId
     *
     * @param string $sourceId
     *
     * @return Nursery
     */
    public function setSourceId($sourceId)
    {
        $this->source_id = $sourceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->source_id;
    }

    /**
     * @return Collection
     */
    public function getSelections()
    {
        return $this->selections;
    }

    /**
     * @param NurserySelection $selection
     */
    public function addSelection(NurserySelection $selection)
    {
        if (!$this->selections->contains($selection)) {
            $selection->setNursery($this);
            $this->selections->add($selection);
        }
    }

    /**
     * @param NurserySelection $selection
     */
    public function removeSelection(NurserySelection $selection) {
        if ($this->selections->contains($selection)) {
            $this->selections->remove($selection);
            $selection->setNursery(null);
        }
    }
}
