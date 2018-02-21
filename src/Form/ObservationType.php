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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\ImageType;
use App\Entity\Bird;
use Doctrine\ORM\EntityManager;                                                    


class ObservationType extends AbstractType
{
  private $em;

  public function __construct(EntityManager $em){
    $this->em = $em;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    
    $builder
    ->add('bird', HiddenType::class, array(
      'attr'=>['class'=>'birdId']
    ))
    ->add('birdSearch', TextType::class, array(
      'mapped'=>false,
      'attr'=>['class'=>"birdSearch"]
    ))

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

        $em = $this->em;
        $builder->get('bird')
        ->addModelTransformer(new CallbackTransformer(
            function ($birdEntity) {
              if(!is_null($birdEntity)){
                return $birdEntity->getId();
              }
              return null;

            },
            function ($birdId) use ($em) {
              return $em->getRepository(Bird::class)->find($birdId);
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
