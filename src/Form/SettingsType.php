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

class SettingsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('email', TextType::class, array(
        'required' => false)
    )
    ->add('avatarFile', VichFileType::class,
            ['required' => false,
            'allow_delete' => false,
            'download_link' => false,])
    ->add('submit', SubmitType::class, [
      'label' => 'Modifier',
      'attr' => ['class' => 'btn btn-primary btn-lg']
    ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
