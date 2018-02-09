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
                'resa'          => $resa,
                'billetResa'    => [$billetResa],
                'billets'       => $billets,
                'prixtotal'     => $resa->getPrixTotal(),
                'form'          => $form->createView()
            ]
        );
    }

    public function panierAction(Request $request, $id)
    {
        $outilsReservation = $this->get('service_container')->get('jd_reservation.outilsreservation');
        $repository =  $this->getDoctrine()->getRepository('JDLouvreBundle:Reservation');
        $resa = $repository->find($id);
        $billetResa = $resa;
        $totalBilletPrix = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Billets::class)
            ->findByReservation($resa);

        $outilsReservation->prixTotal($totalBilletPrix, $resa);
        return $this->render('JDLouvreBundle:LouvreReservation/Panier:panier.html.twig',
            [
                'billetResa'        => [$billetResa],
                'billets'           => $resa,
                'prixtotal'         => $resa->getPrixTotal()
            ]);
    }

    public  function modifieAction(Billets $billets, Request $request, Session $session)
    {
        $outilsBillets = $this->get('service_container')->get('jd_reservation.outilsbillets');
        $outilsReservation = $this->get('service_container')->get('jd_reservation.outilsreservation');
        $resa = $session->get('resa');
        if(null === $resa)
        {
            throw new NotFoundHttpException("Le billet".$resa->getResaCode()." n'existe pas" );
        }

        $form = $this->createForm(BilletsType::class, $billets);
        $form->handleRequest($request);
        dump($billets);
        if ($form->isSubmitted() && $form->isValid())
        {
            $session->set('resa', $resa);
            $billets = $outilsBillets->calculPrix($billets);
            $em = $this->getDoctrine()
                ->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Votre billet a été bien modifiée');;
            return $this->redirectToRoute('jd_reservation_panier',
                [
                    'resacode'    => $resa->getResaCode(),
                    'id' =>$resa->getId()
                ]);
        }

        dump($resa);
        return $this->render('JDLouvreBundle:LouvreReservation/Edit:modifie.html.twig',
            [
                'resa'      => $resa,
                'form'      => $form->createView()
            ]);
    }

    public function addAction(Request $request, Session $session, Reservation $reservation)
    {
        $resa = $session->get('resa');

        $em = $this
            ->getDoctrine()
            ->getManager();

        if(null === $resa)
        {
            throw  new NotFoundHttpException("Se billet n°: " .$resa->getResaCode(). " n'exite pas ");
        }
        $nb = $reservation->getNbBillets();

        $reservation->setNbBillets($nb + 1);
        $resa = $reservation;
        $session->set('resa', $resa);
        $em->persist($reservation);
        $em->flush();
        $request->getSession()->getFlashBag()->add('info', "Vous avez ajouté un billet aux nombre de billets ".$reservation->getNbBillets());
        return $this->redirectToRoute('jd_reservation_startBillets',
            [
                'resacode'    => $resa->getResaCode(),
                'id'    => $resa->getId()
            ]
        );
    }


    public function deletAction(Request $request, Session $session, Reservation $reservation)
    {
        $resa = $session->get('resa');

        $em = $this
            ->getDoctrine()
            ->getManager();

        if(null === $resa)
        {
            throw  new NotFoundHttpException("Se billet n°: " .$resa->getResaCode(). " n'exite pas ");
        }

        $nb = $reservation->getNbBillets();
        $reservation->setNbBillets($nb - 1);
        $nb = $reservation->getNbBillets();

        if($nb > 1)
        {
            $resa = $reservation;
            $session->set('resa', $resa);
            $em->persist($reservation);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le nombre de billet a bien été modifié." .$reservation->getNbBillets());
            return $this->redirectToRoute('jd_reservation_startBillets',
                [
                    'resacode'    => $resa->getResaCode(),
                    'id'    => $resa->getId()
                ]
            );
        }
        elseif($nb == 1)
        {
            $resa = $reservation;
            $session->set('resa', $resa);
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('jd_reservation_panier',
                [
                    'id' =>$resa->getId()
                ]);
        }
    }
}