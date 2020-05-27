<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {/*De builder check naar de variabele die hij binnenkrijgt*/
        $builder
            ->add('username', TextType::class, [
                'label' => 'Email',
            ])
            ->add('naam')
            ->add('telefoon_nummer')
            ->add('geboorte_datum', BirthdayType::class,[
                'input'  => 'string',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('geslacht', ChoiceType::class, [
                'choices' => [
                    'Man' => 'm',
                    'Vrouw' => 'w',
                ]
            ])
            ->add('adres')
            ->add('woonplaats')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'U moet de terms accepten.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'U heeft geen password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Jouw password moet minimaal {{ limit }} characters zijn.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
