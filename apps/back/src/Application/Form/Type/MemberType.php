<?php

declare(strict_types=1);

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class MemberType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('member.form.name'),
                'attr' => [
                    'placeholder' => $this->translator->trans('common.name'),
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('name.min', ['%limit%' => '2'], 'validators'),
                        'max' => 50,
                        'maxMessage' => $this->translator->trans('name.max', ['%limit%' => '50'], 'validators'),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => $this->translator->trans('member.form.email'),
                'attr' => [
                    'placeholder' => $this->translator->trans('member.form.email_placeholder'),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'options' => ['attr' => ['placeholder' => '********']],
                'first_options' => ['label' => $this->translator->trans('member.form.password')],
                'second_options' => ['label' => $this->translator->trans('member.form.password_confirm')],
            ]);
    }
}
