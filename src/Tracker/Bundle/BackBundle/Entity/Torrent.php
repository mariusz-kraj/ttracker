<?php

namespace Tracker\Bundle\BackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Torrent
 *
 * @ORM\Table(
 *     name="torrents"
 * )
 * @ORM\Entity(repositoryClass="Tracker\Bundle\BackBundle\Entity\Repositories\TorrentRepository")
 */
class Torrent implements ItemInterface
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
     * @var \Tracker\Back\Entity\Episode
     *
     * @ORM\ManyToOne(targetEntity="\Tracker\Bundle\BackBundle\Entity\Episode", inversedBy="torrents")
     * @ORM\JoinColumn(name="episode", referencedColumnName="id")
     */
    private $episode;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="text")
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="magnet", type="text")
     */
    private $magnet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     * 0 => undefined
     * 1 => LD (360p, 240p, R5, R6, CAM...)
     * 2 => MD (576p, 480p, 480i)
     * 3 => HD (720p, 1080p)
     *
     * @ORM\Column(name="quality", type="integer")
     */
    private $quality = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="verified", type="boolean")
     */
    private $verified;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var integer
     *
     * @ORM\Column(name="seeds", type="smallint")
     */
    private $seeds;

    /**
     * @var integer
     *
     * @ORM\Column(name="peers", type="smallint")
     */
    private $peers;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(
            new UniqueEntity(
                array(
                    'fields'  => 'hash',
                    'message' => 'Ten torrent znajduje sie juz w bazie.',
                )
            )
        );

        $metadata->addPropertyConstraint(
            'episode',
            new Assert\NotBlank(
                array(
                    'message' => 'Torrent musi byc powiazany z serialem'
                )
            )
        );
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
     * Set episode
     *
     * @param \stdClass $episode
     * @return Torrent
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * Get episode
     *
     * @return \stdClass
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Torrent
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
     * Set link
     *
     * @param string $link
     * @return Torrent
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set magnet
     *
     * @param string $magnet
     * @return Torrent
     */
    public function setMagnet($magnet)
    {
        $this->magnet = $magnet;

        return $this;
    }

    /**
     * Get magnet
     *
     * @return string
     */
    public function getMagnet()
    {
        return $this->magnet;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Torrent
     */
    public function setDate($date)
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set quality
     *
     * @param integer $quality
     * @return Torrent
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return integer
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return Torrent
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Torrent
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
     * Set seeds
     *
     * @param integer $seeds
     * @return Torrent
     */
    public function setSeeds($seeds)
    {
        $this->seeds = $seeds;

        return $this;
    }

    /**
     * Get seeds
     *
     * @return integer
     */
    public function getSeeds()
    {
        return $this->seeds;
    }

    /**
     * Set peers
     *
     * @param integer $peers
     * @return Torrent
     */
    public function setPeers($peers)
    {
        $this->peers = $peers;

        return $this;
    }

    /**
     * Get peers
     *
     * @return integer
     */
    public function getPeers()
    {
        return $this->peers;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Torrent
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function getFeedItemTitle()
    {
        return $this->getTitle();
    }

    public function getFeedItemDescription()
    {
        return $this->getTitle();
    }

    public function getFeedItemPubDate()
    {
        return $this->getDate();
    }

    public function getFeedItemLink()
    {
        return $this->getMagnet();
    }
}