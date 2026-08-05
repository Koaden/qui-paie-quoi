<?php

declare(strict_types=1);

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('common.name'),
                'attr' => [
                    'placeholder' => $this->translator->trans('group.name'),
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
            ->add('description', TextareaType::class, [
                'label' => $this->translator->trans('group.description'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('group.describe'),
                    'rows' => 4,
                ],
            ]);
    }
}
