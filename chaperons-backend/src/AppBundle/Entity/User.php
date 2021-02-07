<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="apiKey", type="string", length=255, unique=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @Exclude()
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Exclude()
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Map", mappedBy="user", cascade={"remove"})
     */
    private $maps;

    public function __construct()
    {
        $this->maps = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getSalt()
    {
    }
    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password
            ) = unserialize($serialized);
    }

    public static function generateApiKey($length=7) {
        $validCharacters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $validCharNumber = strlen($validCharacters);
        $key = "";

        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $key .= $validCharacters[$index];
        }
        return $key;
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
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Add map
     *
     * @param \AppBundle\Entity\Map $map
     *
     * @return User
     */
    public function addMap(\AppBundle\Entity\Map $map)
    {
        $this->maps[] = $map;

        return $this;
    }

    /**
     * Remove map
     *
     * @param \AppBundle\Entity\Map $map
     */
    public function removeMap(\AppBundle\Entity\Map $map)
    {
        $this->maps->removeElement($map);
    }

    /**
     * Get maps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMaps()
    {
        return $this->maps;
    }
}
