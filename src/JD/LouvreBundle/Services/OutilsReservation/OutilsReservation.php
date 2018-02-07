<?php
namespace JD\LouvreBundle\Services\OutilsReservation;

use JD\LouvreBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager as ORM;


class OutilsReservation
{
    private $em;
    private $session;

    public function __construct($em, $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @param $resaCode
     * @param $nouvelleResaAcceptee
     * @return Reservation|null
     */
    public function reservationInitial($resaCode, $nouvelleResaAcceptee)
    {
        $resa = null;

        if ($resaCode !== null )
        {
            $resa = $this->em->getRepository('JDLouvreBundle:Reservation')->findOneBy(array(
                'resaCode' => $resaCode
            ));
        }

        // si le controlleur permet la création d'une nouvelle réservation
        if ($resa === null && $nouvelleResaAcceptee)
        {
            $resa = new Reservation();
        }elseif($resa === null)
        {
            return null;
        }
        return $resa;
    }

    public function  reservationValider(Reservation $resa)
    {
        $reservationValide = true;
        try
        {
            $this->em->persist($resa);
            $this->em->flush();
            $reservationValide = true;
        }
        catch (Exception $e)
        {
            $this->session->getFlashBag()->add('erreurInterne', "Une erreur interne s'est produite, merci de réessayer.");
            $reservationValide = false;
        }
        return $reservationValide;
    }


    /**
     * @param $billets
     * @param $reservation
     */
    public function prixTotal($billets, $reservation){
        $total = 0;
        foreach ($billets as  $billet){
            $total = $total + $billet->getPrix();
        }
        $reservation->setPrixtotal($total);
        $this->em->persist($reservation);
        $this->em->flush();
    }
}