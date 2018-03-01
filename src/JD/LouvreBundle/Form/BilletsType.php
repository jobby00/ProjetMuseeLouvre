<?php
namespace JD\LouvreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('dateNaissance', DateType::class,
                [
                    'days'      => range(1, 31),
                    'months'    => range(1, 12),
                    'years'     => range(1902, date('Y')),
                    'format'    => 'dd-MM-yyyy'
                ])
            ->add('pays', CountryType::class,
                [
                    'preferred_choices'      => ['FR']
                ])
            ->add('dateresa', DateType::class,
                [
                    'widget'        => 'single_text',
                    'input'         => 'datetime',
                    'format'        => 'dd/MM/yyyy'
                ])
            ->add('demijournee', ChoiceType::class,
                [
                    'choices'            => [
                        'Journée'       => false,
                        'Demi-Journée'  => true
                    ]
                ])
            ->add('tarifReduit', CheckboxType::class,
                [
                    'label'     => 'Tarif rédui ?',
                    'required'  => false
                ])
            ->add('Suivant', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JD\LouvreBundle\Entity\Billets'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jd_louvrebundle_billets';
    }


}
