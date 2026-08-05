<?php

declare(strict_types=1);

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class, [
                'required' => true,
                'label' => $this->translator->trans('member.form.email'),
                'attr' => [
                    'placeholder' => $this->translator->trans('member.form.email_placeholder'),
                ],
            ])
            ->add('_password', PasswordType::class, [
                'required' => true,
                'label' => $this->translator->trans('member.form.password'),
                'attr' => [
                    'placeholder' => '********',
                ],
            ])
            ->add('_csrf_token', HiddenType::class, [
                'mapped' => false,
                'data' => $this->csrfTokenManager->getToken('authenticate')->getValue(),
                'attr' => [
                    'data-controller' => 'csrf-protection',
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
