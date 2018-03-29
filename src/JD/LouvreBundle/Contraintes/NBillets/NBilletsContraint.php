<?php
namespace JD\LouvreBundle\Contraintes\NBillets;

use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */
class NBilletsContraint extends Constraint
{
    public $message = 'Vous pouvez reserver de 1 à 20 billets. S\'il vous en faut plus contacter nous Par email: contact@louvre.fr  ou à ce n° tél: +33156488956 ';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}