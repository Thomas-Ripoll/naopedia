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
    ->add('username', TextType::class)
    ->add('email', TextType::class)
    ->add('password', RepeatedType::class, array(
        'type'            => PasswordType::class,
         //'invalid_message' => 'The password fields must match.',
         'options'         => array('required' => true),
         'first_options'   => array('label' => 'Password'),
         'second_options'  => array('label' => 'Repeat password'),
     ))
    ->add('bio', TextareaType::class)
    ->add('avatarFile', VichFileType::class,
            ['required' => false,
            'allow_delete' => true,
            'download_link' => true,])
    ->add('submit', SubmitType::class, [
      'label' => 'Create',
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
