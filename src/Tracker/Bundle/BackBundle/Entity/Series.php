<?php

namespace Tracker\Bundle\BackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Series
 *
 * @ORM\Table(
 *     name="series"
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Tracker\Bundle\BackBundle\Entity\Repositories\SeriesRepository")
 *
 */
class Series
{
    /**
     * @var integer
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
     * @ORM\Column(name="normalizedName", type="string", length=255, nullable=true)
     */
    private $normalizedName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="\Tracker\Bundle\BackBundle\Entity\Episode", mappedBy="series")
     * @ORM\OrderBy({"season" = "DESC","number" = "DESC"})
     */
    private $episodes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setNormalizedName($this->getName());
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setNormalizedName($this->getName());
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
     * @return Series
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
     * Set normalizedName
     *
     * @param string $normalizedName
     * @return Series
     */
    public function setNormalizedName($normalizedName)
    {
        $normalizedName = preg_replace('/\s/', '', $normalizedName);
        $normalizedName = preg_replace('/[^a-zA-Z0-9]/', '', $normalizedName);
        $normalizedName = mb_strtolower($normalizedName);

        $this->normalizedName = $normalizedName;

        return $this;
    }

    /**
     * Get normalizedName
     *
     * @return string 
     */
    public function getNormalizedName()
    {
        return $this->normalizedName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Series
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
    
    /**
     * Add episodes
     *
     * @param \Tracker\BackBundle\Entity\Episode $episodes
     * @return Series
     */
    public function addEpisode(\Tracker\Bundle\BackBundle\Entity\Episode $episodes)
    {
        $this->episodes[] = $episodes;
    
        return $this;
    }

    /**
     * Remove episodes
     *
     * @param \Tracker\BackBundle\Entity\Episode $episodes
     */
    public function removeEpisode(\Tracker\Bundle\BackBundle\Entity\Episode $episodes)
    {
        $this->episodes->removeElement($episodes);
    }

    /**
     * Get episodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }
}