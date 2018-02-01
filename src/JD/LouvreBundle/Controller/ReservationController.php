<?php
namespace JD\LouvreBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class ReservationController extends  Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('JDLouvreBundle:LouvreReservation:index.html.twig');
    }
    /**
     * @return Response
     */
    public function startReservationAction()
    {
        return $this->render('JDLouvreBundle:LouvreReservation/Reservation:startReservation.html.twig');
    }
    /**
     * @return Response
     */
    public function startBilletsAction()
    {
        return $this->render('JDLouvreBundle:LouvreReservation/Billets:startBillets.html.twig');
    }
    /**
     * @return Response
     */
    public function panierAction()
    {
        return $this->render('JDLouvreBundle:LouvreReservation/Panier:panier.html.twig');
    }
}