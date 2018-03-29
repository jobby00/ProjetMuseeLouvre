<?php
namespace JD\LouvreBundle\Contraintes\NBillets;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NBilletsContraintValidator extends ConstraintValidator
{
    public function  Validate($value, Constraint $constraint)
    {
        if(range(1, 20))
        {
            return true;
        }else{
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)->addViolation();
        }
    }
}