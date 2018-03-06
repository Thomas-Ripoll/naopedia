<?php

namespace App\Form;                                                    

use App\Entity\Bird;
use App\Entity\Observation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ObservationType extends AbstractType
{
  private $em;

  public function __construct(EntityManager $em){
    $this->em = $em;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    
    $builder
    ->add('geoloc', HiddenType::class)
    ->add('bird', HiddenType::class, array(
      'attr'=>['class'=>'birdId']
    ))
    ->add('description', TextareaType::class,[
        "attr"=>[
            "class"=>"form-control"
        ]
    ])
    ->add('image', ImageNoAltType::class,[
        
        "attr"=>[
            "id"=>"file-upload"
        ]
    ])
    ->add('submit', SubmitType::class, [
      'label' => 'Envoyer Observation',
      'attr' => ['class' => 'btn btn-primary btn-block'],
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
              return $birdId ? $em->getRepository(Bird::class)->find($birdId):null;
            }
        ))
    ;

  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Observation::class,
      'attr' => ["id" => "post-observation-form" ] 
    ]);
  }
}
