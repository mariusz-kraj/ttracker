<?php

namespace Tracker\Bundle\BackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Episode
 *
 * @ORM\Table(
 *     name="episodes"
 * )
 * @ORM\Entity(repositoryClass="Tracker\Bundle\BackBundle\Entity\Repositories\EpisodeRepository")
 */
class Episode
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
     * @var \Tracker\Bundle\BackBundle\Entity\Series
     * @ORM\ManyToOne(targetEntity="\Tracker\Bundle\BackBundle\Entity\Series", inversedBy="episodes")
     * @ORM\JoinColumn(name="series", referencedColumnName="id")
     */
    private $series;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="season", type="integer", nullable=true)
     */
    private $season;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @ORM\OneToMany(targetEntity="\Tracker\Bundle\BackBundle\Entity\Torrent", mappedBy="episode")
     * @ORM\OrderBy({"seeds" = "DESC"})
     */
    private $torrents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->torrents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set series
     *
     * @param \stdClass $series
     * @return Episode
     */
    public function setSeries($series)
    {
        $this->series = $series;
    
        return $this;
    }

    /**
     * Get series
     *
     * @return \stdClass 
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Episode
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set season
     *
     * @param integer $season
     * @return Episode
     */
    public function setSeason($season)
    {
        $this->season = $season;
    
        return $this;
    }

    /**
     * Get season
     *
     * @return integer 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Episode
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }
    
    /**
     * Add torrents
     *
     * @param \Tracker\Bundle\BackBundle\Entity\Torrent $torrents
     * @return Episode
     */
    public function addTorrent(\Tracker\Bundle\BackBundle\Entity\Torrent $torrents)
    {
        $this->torrents[] = $torrents;
    
        return $this;
    }

    /**
     * Remove torrents
     *
     * @param \Tracker\Bundle\BackBundle\Entity\Torrent $torrents
     */
    public function removeTorrent(\Tracker\Bundle\BackBundle\Entity\Torrent $torrents)
    {
        $this->torrents->removeElement($torrents);
    }

    /**
     * Get torrents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTorrents()
    {
        return $this->torrents;
    }
}