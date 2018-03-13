<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('username', TextType::class, array(
      'label'=>"Pseudo",
    ))
    ->add('email', TextType::class,array(
      'label'=>"Email",))
    ->add('password', RepeatedType::class, array(
        'type'            => PasswordType::class,
         //'invalid_message' => 'The password fields must match.',
         'options'         => array('required' => true),
         'first_options'   => array('label' => 'Mot de passe'),
         'second_options'  => array('label' => 'Repeter mot de passe'),
     ))
    ->add('bio', TextareaType::class, array(
      'label'=>"Ajouter une descrption"
    ))
    ->add('avatarFile', VichFileType::class,
    array("label"=>"Ajouter une image de profil"),
            ['required' => false,
            'allow_delete' => false,
            'download_link' => false,]
    )
    ->add('submit', SubmitType::class, [
      'label' => "S'INSCRIRE",
      'attr' => ['class' => 'btn btn-primary btn-lg'],
    ])
    ;

  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
