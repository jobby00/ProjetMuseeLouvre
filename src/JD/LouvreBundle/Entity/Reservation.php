<?php

namespace JD\LouvreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * Resrvation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="JD\LouvreBundle\Repository\ReservationRepository")
 */
class Reservation
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
     * @var \DateTime
     *
     * @ORM\Column(name="datecreate", type="datetime")
     * @Assert\DateTime()
     * @Assert\GreaterThanOrEqual("today")
     */
    private $datecreate;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="nbBillets", type="integer")
     * @ASSERT\Range(
     *     min = 1,
     *     max = 20
     * )
     */
    private $nbBillets = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="resa_code", type="string")
     */
    private $resaCode;

    /**
     * @var float
     *
     * @ORM\Column(name="prixTotal", type="float")
     */
    private $prixTotal = 0;

    /**
     * @ORM\OneToMany(targetEntity="JD\LouvreBundle\Entity\Billets", mappedBy="reservation")
     * @Assert\Valid
     */
    private $billets;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->email = "";
        $this->datecreate = new \DateTime("now", new \DateTimeZone('Europe/Paris'));
        //Generation Du Code dela Reservation
        $lettres = 'AZERTYUIOPQSDFGHJKLMWXCVBNAZERTYUIOPQSDFGHJKLMWXCVBN';
        $lettres = str_split(str_shuffle($lettres), 6)[0];
        $chifres = rand(100000, 999999);
        $this->resaCode = str_split(str_shuffle($chifres.$lettres),12)[0];
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
     * Set datecreate
     *
     * @param \DateTime $datecreate
     *
     * @return Reservation
     */
    public function setDatecreate($datecreate)
    {
        $this->datecreate = $datecreate;

        return $this;
    }

    /**
     * Get datecreate
     *
     * @return \DateTime
     */
    public function getDatecreate()
    {
        return $this->datecreate;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Reservation
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
     * Set nbBillets
     *
     * @param integer $nbBillets
     *
     * @return Reservation
     */
    public function setNbBillets($nbBillets)
    {
        $this->nbBillets = $nbBillets;

        return $this;
    }

    /**
     * Get nbBillets
     *
     * @return int
     */
    public function getNbBillets()
    {
        return $this->nbBillets;
    }

    /**
     * Set resaCode
     *
     * @param string $resaCode
     *
     * @return Reservation
     */
    public function setResaCode($resaCode)
    {
        $this->resaCode = $resaCode;

        return $this;
    }
    /**
     * Get resaCode
     *
     * @return string
     */
    public function getResaCode()
    {
        return $this->resaCode;
    }

    /**
     * Add billet
     *
     * @param \JD\LouvreBundle\Entity\Billets $billet
     *
     * @return Reservation
     */
    public function addBillet(\JD\LouvreBundle\Entity\Billets $billet)
    {
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \JD\LouvreBundle\Entity\Billets $billet
     */
    public function removeBillet(\JD\LouvreBundle\Entity\Billets $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * Set prixTotal
     *
     * @param float $prixTotal
     *
     * @return Reservation
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    /**
     * Get prixTotal
     *
     * @return float
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }
}
