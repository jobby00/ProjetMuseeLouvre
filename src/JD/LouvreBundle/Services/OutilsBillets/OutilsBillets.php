<?php
namespace JD\LouvreBundle\Services\OutilsBillets;


use Doctrine\ORM\EntityManager;
use JD\LouvreBundle\Entity\Billets;
use JD\LouvreBundle\Entity\Reservation;
use JD\LouvreBundle\JDLouvreBundle;
use Symfony\Component\HttpFoundation\Session\Session;

class OutilsBillets
{
    private $ageMaxGratuit  = 4;
    private $ageMaxEnfant   = 12;
    private $ageMinSenior   = 60;
    private $tarifEnfant    = 8;
    private $tarifSenior    = 12;
    private $tarifNormal    = 16;
    private $em;
    private $session;

    public  function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Billets $billets
     * @param $resa
     * @return bool|Billets
     */
    public  function validerBillet($billets, $resa)
    {
        $em = $this->em;
        try
        {
            $resa->addBillet($billets);
            $em->persist($resa);
            $em->persist($billets);
            $em->flush();
            $billets = true;
        }catch (Exception $e)
        {
            $this->session->getFlashBag()->add('erreurInterne', "Une erreur interne s'est produite, merci de réessayer.");
            $billets = false;
        }
        return $billets;
    }

    /**
     * retourne l'age en fonction de la date de naissance en datetime
     *
     * @param datetime $dateNaissance
     * @return int $age
     */
    public function calculAge($dateNaissance){

        $age = idate('Y') - $dateNaissance->format('Y');

        return $age;
    }

    /**
     * retourne le tarif du billet en fonction de la date de naissance
     *
     *
     * @param Billet $billet
     * @return boolean
     * @internal param $dateNaissance
     */
    public function calculPrix($billets){

        $dateNaissance = $billets->getDateNaissance();

        $age = $this->calculAge($dateNaissance);

        if ( $age <= $this->ageMaxGratuit ){
            $prix = 0;
        }
        elseif ( $age <= $this->ageMaxEnfant )
        {
            $prix = $this->tarifEnfant = 8;
        }
        elseif( $age >= $this->ageMinSenior)
        {
            $prix = $this->tarifSenior = 12;
        }
        elseif ( $billets->getTarifReduit() )
        {
            $prix = $this->tarifReduit = 10;
        }
        else
        {
            $prix = $this->tarifNormal = 16;
        }
        $billets->setPrix($prix);
        return $billets;
    }
}