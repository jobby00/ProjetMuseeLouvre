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
    public function startReservationAction(Request $request, $resaCode)
    {
        $session = new Session();
        //Initialisation du SERVICE OutilsReservayion
        $outilsReservation = $this->get('service_container')->get('jd_reservation.outilsreservation');
        $resa = $outilsReservation->reservationInitial($resaCode, true);

        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $resa = $form->getData();
            $session->set('resa', $resa);
            if($outilsReservation->reservationValider($resa))
            {
                $request->getSession()->getFlashBag()->add('notice', 'Vous avez bien demarrer votre reservation, vous ètres à l\'Etape 2');
                return $this->redirectToRoute('jd_reservation_startBillets',
                    [
                        'resacode'  => $resa->getResaCode(),
                        'id'        => $resa->getId()
                    ]);
            }
        }

        return $this->render('JDLouvreBundle:LouvreReservation/Reservation:startReservation.html.twig',
            [
                'form'          => $form->createView()
            ]);
    }
    /**
     * @return Response
     */
    public function startBilletsAction(Session $session, Request $request, Reservation $resa)
    {
        $billets = new Billets();
        // intialisation  des billets
        $outilsBillets = $this->get('service_container')
            ->get('jd_reservation.outilsbillets');


        // création du formulaire associé à cette réservation + requête
        $form = $this->createForm(BilletsType::class, $billets);
        $form->handleRequest($request);

        // action lors de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid())
        {
            $session->set('resa', $resa);
            $billets->setReservation($resa);
            $billets = $outilsBillets->calculPrix($billets);
            if ($outilsBillets->validerBillet($billets, $resa))
            {
                $totalBillet = 0;
                foreach ($resa->getBillets() as $billet)
                {
                    $totalBillet ++;
                }
                if ($resa->getNbBillets() != $totalBillet) {
                    //après validation, transfert vers l'étape suivante avec les paramètres de la résa
                    return $this->redirectToRoute('jd_reservation_startBillets',
                        [
                            'resacode'    => $resa->getResaCode(),
                            'id'    => $resa->getId()
                        ]);
                } else {
                    return $this->redirectToRoute('jd_reservation_panier',
                        [
                            'resacode'    => $resa->getResaCode(),
                            'id'    => $resa->getId()
                        ]);
                }
            }
        }
        $outilsReservation = $this->get('service_container')->get('jd_reservation.outilsreservation');
        $totalBilletPrix = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Billets::class)
            ->findByReservation($resa);

        $outilsReservation->prixTotal($totalBilletPrix, $resa);
        $billetResa = $resa;
        $resa->getBillets();
        $resa = $session->get('resa');
        dump($resa);
        return $this->render('JDLouvreBundle:LouvreReservation/Billets:startBillets.html.twig',
            [
                'billetResa'    => [$billetResa],
                'billets'       => $billets,
                'prixtotal'     => $resa->getPrixTotal(),
                'form'          => $form->createView()
            ]
        );
    }

    public function panierAction(Request $request, Session $session, $id)
    {
        $resa = $session->get('resa');
        $billetResa = $resa;
        return $this->render('JDLouvreBundle:LouvreReservation/Panier:panier.html.twig',
            [
                'billetResa'        => [$billetResa],
                'billets'           => $resa,
                'prixtotal'         => $resa->getPrixTotal()
            ]);
    }
}