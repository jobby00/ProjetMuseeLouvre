<?php
namespace JD\LouvreBundle\Contraintes\NbBillets;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JD\LouvreBundle\Entity\Billets;
use JD\LouvreBundle\Entity\Reservation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Tests\Fixtures\ConstraintAValidator;

class NbBilletsContraintValidator extends ConstraintAValidator
{
    private $nbBilletsMaxParJour = 1;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if($value->getPayer())
        {
            foreach ($value->getBillets() as $billet)
            {
                $nbBilleReserves = $this->em->getRepository('JDLouvreBundle:Billets')
                    ->countByDateResa($billet->getDateResa());
                    dump($nbBilleReserves);
                if ($nbBilleReserves + 1 > $this->nbBilletsMaxParJour) {
                    $this->context->buildViolation($constraint->messageNbBillets)
                        ->setParameter('{{date}}', $billet->getDateResa()->format('d/m/Y'))
                        ->addViolation();
                }
            }
        }

    }
}