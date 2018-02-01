<?php
namespace JD\LouvreBundle\Controller;

use JD\LouvreBundle\Entity\Billets;
use JD\LouvreBundle\Entity\Reservation;
use JD\LouvreBundle\Form\BilletsType;
use JD\LouvreBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function startReservationAction(Request $request)
    {
        $reservation = new Reservation();
        $session = new Session();

        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $reservation = $form->getData();
            $resa = $reservation;
            $session->set('resa', $resa);
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();

            dump($reservation);
            dump($resa);
            $request->getSession()->getFlashBag()->add('notice', 'Vous avez bien demarrer votre reservation, vous ètres à l\'Etape 2');
            return $this->redirectToRoute('jd_reservation_startBillets',
                [
                    'id'        => $resa->getId()
                ]);
        }

        return $this->render('JDLouvreBundle:LouvreReservation/Reservation:startReservation.html.twig',
            [
                'form'          => $form->createView()
            ]);
    }
    /**
     * @return Response
     */
    public function startBilletsAction(SessionInterface $session, Request $request)
    {
        $resa = $session;
        $billets = new Billets();

        $form = $this->createForm(BilletsType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $billets = $form->getData();
            $resa = $billets;
            $session->set('resa', $resa);
            $em = $this->getDoctrine()->getManager();
            $em->persist($resa);
            $em->persist($billets);
            $em->flush();

            dump($billets);
            dump($resa);
            die();
            $request->getSession()->getFlashBag()->add('notice', 'Vous avez bien demarrer votre reservation, vous ètres à l\'Etape 3');
            return $this->redirectToRoute('jd_reservation_panier');
        }
        return $this->render('JDLouvreBundle:LouvreReservation/Billets:startBillets.html.twig',
            [
                'form'          => $form->createView()
            ]);
    }
    /**
     * @return Response
     */
    public function panierAction()
    {
        return $this->render('JDLouvreBundle:LouvreReservation/Panier:panier.html.twig');
    }
}