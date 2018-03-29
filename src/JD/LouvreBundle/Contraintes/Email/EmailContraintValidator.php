<?php
namespace JD\LouvreBundle\Contraintes\Email;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailContraintValidator extends ConstraintValidator
{
    public function Validate($value, Constraint $constraint)
    {
        if(!preg_match('/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/', $value, $matches))
        {
            $this->context->buildViolation($constraint->messageCesi)
                ->setParameter('{{string}}', $value)->addViolation();
        }
    }
}