<?php

namespace App\Form;

use App\Entity\Animal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AnimalType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tipo', TextType::class, ['label' => 'Tipo de animal'] )
                ->add('color', TextType::class)
                ->add('raza', TextType::class)
                ->add('save', SubmitType::class, [
                        'label' => 'Crear Animal',
                        'attr' => [ 'class' => 'btn btn-success']
                     ]);

                     /* Comentado del Controlador AnimalController */

                     /*  $form = $this->createFormBuilder($animal)
                        // ->setAction($this->generateUrl('animal_save')) // PODRÍA hacerlo así, y en la función save() coger por POST los datos y hacer el save.
                        // ->setMethod('POST') // Por DEFAULT es POST 
                            ->add('tipo', TextType::class, ['label' => 'Tipo de animal'] )
                            ->add('color', TextType::class)
                            ->add('raza', TextType::class)
                            ->add('save', SubmitType::class, [
                                'label' => 'Crear Animal',
                                'attr' => [ 'class' => 'btn btn-success']
                            ])
                        ->getForm(); */
                    
    }
}

?>
