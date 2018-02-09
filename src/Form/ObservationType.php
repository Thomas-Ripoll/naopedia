<?php

namespace App\Form;

use App\Entity\Observation;
use App\Entity\Image;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\ImageType;
use App\Entity\Bird;


class ObservationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('bird', EntityType::class, array(
    'class' => Bird::class,
    'choice_label' => 'name'))
    ->add('image', ImageType::class)
    ->add('geoloc', TextType::class)
    ->add('date', DateType::class)
    ->add('submit', SubmitType::class, [
      'label' => 'Create',
      'attr' => ['class' => 'btn btn-default pull-right'],
    ])
    ;

    $builder->get('geoloc')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                  if(is_array($tagsAsArray)){
                    // transform the array to a string
                    return implode('/', $tagsAsArray);
                  }
                  return ('');

                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return explode('/', $tagsAsString);
                }
            ))
        ;

  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Observation::class,
    ]);
  }
}
