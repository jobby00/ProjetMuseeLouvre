<?php
namespace JD\LouvreBundle\Services\OutilsBillets;

use Doctrine\ORM\EntityManager;
use JD\LouvreBundle\Entity\Billets;
use JD\LouvreBundle\Entity\Reservation;
use \DateTime;

class OutilsBillets
{
    private $ageMaxGratuit  = 4;
    private $ageMaxEnfant   = 12;
    private $ageMinSenior   = 60;
    private $tarifEnfant    = 8;
    private $tarifSenior    = 12;
    private $tarifNormal    = 16;
    private  $heureLimiteDemiJournee = 14;
    private  $nbBilletsMaxParJour = 1000;
    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function verifDate(Billets $billets)
    {
        $dateResa = $billets->getDateresa();
        $dateUsuelle = new DateTime("now", new \DateTimeZone('Europe/Paris'));
        $dateResaForma = $dateResa->format('dm');
        $dateResaWeeck = $dateResa->format('w');
        if(
            $dateResaForma == "0105"
            || $dateResaForma == "2512"
            || $dateResaWeeck == '0'
            || $dateResaWeeck == '2'
        )
        {
            $this->session->getFlashBag()->add('erreurInterne', "Nous sommes désolé le musée n'est pas ouvert à cette date.");
            return false;
        }elseif (
            !$billets->getDemijournee()
            && $dateResa->format('dmY') == $dateUsuelle->format('dmY')
            && $dateUsuelle->format('H') >= $this->heureLimiteDemiJournee)
        {
            $this->session->getFlashBag()->add('erreurInterne', 'Nous sommes désolé vous ne pouvez plus sélectionner une réservation journée pour le jour même après 14h!');
            return false;
        }else
        {
            return true;
        }
    }

    public function verifNbPlaces($date, $nbBillets = 1)
    {
        $nbBilleReserves = $this->em->getRepository('JDLouvreBundle:Billets')
           ->countByDateResa($date);

       if ($nbBilleReserves + $nbBillets <= $this->nbBilletsMaxParJour){
           return true;
       }else{
           $this->session->getFlashBag()->add('erreurInterne', "Nous sommes désolé, il reste seulement X billet(s) disponibles à la date demandée!");
           return false;
       }
    }

    /**
    public function verifNbPlaces(Billets $billets, Reservation $resa)
    {
        $billetDispo =  true;
        $nbBilleReserves = $this->getDoctrine()->getManager()
            ->getRepository('JDLouvreBundle:Billets')
            ->findByDateResa($billets->getDateResa());
        $sombillets = 0;
        foreach ($nbBilleReserves as $nbBilleReserve)
        {
            $nbBilleReserve = $resa->getNbBillets();
            $sombillets += $nbBilleReserve;
        }
        $nbilletDisponible = $this->nbBilletsMaxParJour - $sombillets;
        if($nbilletDisponible < 1)
        {
            dump('test4');
            $this->session->getFlashBag()->add('erreurInterne', "Nous sommes désolé, il n'y a plus de billet disponible à la date demandée!");
            $billetDispo = false;
        }elseif ($nbilletDisponible < $resa->getNbBillets())
        {
            dump('test5');
            $this->session->getFlashBag()->add('erreurInterne', "Nous sommes désolé, il reste seulement ".$nbilletDisponible." billet(s) disponibles à la date demandée!");
            $billetDispo = false;
        }
        return $billetDispo;
    }
**/
    /**
     * @param Billets $billets
     * @param $resa
     * @return bool|Billets
     */
    public  function validerBillet($billets, $resa)
    {
        if(!$this->verifDate($billets) || !$this->verifNbPlaces($billets->getDateResa()))
        {
            $validerBillet = false;
            return $validerBillet;
        }else{
            return true;
        }
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
        return $prix;
    }
}