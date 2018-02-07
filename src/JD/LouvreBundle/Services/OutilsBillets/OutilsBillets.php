<?php
namespace JD\LouvreBundle\Services\OutilsBillets;


use Doctrine\ORM\EntityManager;
use JD\LouvreBundle\Entity\Billets;
use JD\LouvreBundle\Entity\Reservation;
use JD\LouvreBundle\JDLouvreBundle;
use Symfony\Component\HttpFoundation\Session\Session;

class OutilsBillets
{
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
}