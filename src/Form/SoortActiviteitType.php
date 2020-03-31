<?php


namespace App\Form;


use App\Entity\Soortactiviteit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoortActiviteitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam', TextType::class)
            ->add('minLeeftijd',IntegerType::class)
            ->add('tijdsduur', IntegerType::class)
            ->add('prijs', IntegerType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Soortactiviteit::class,
        ));

    }
}