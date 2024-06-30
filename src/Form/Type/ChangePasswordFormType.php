<?php
/**
 * Change Password Form Type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ChangePasswordFormType.
 *
 * Form type for changing user password.
 */
class ChangePasswordFormType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator translator service for translating validation messages
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options for the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Obecne hasło',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('validators.current_password'),
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Nowe hasło'],
                'second_options' => ['label' => 'Powtórz nowe hasło'],
                'invalid_message' => $this->translator->trans('validators.password_mismatch'),
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('validators.new_password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('validators.password_length', ['{{ limit }}' => 6]),
                        'max' => 4096,
                    ]),
                    new NotCompromisedPassword(),
                ],
            ]);
    }

    /**
     * Configures the form options.
     *
     * @param OptionsResolver $resolver The resolver for form options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
