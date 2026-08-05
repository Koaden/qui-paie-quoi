<?php

declare(strict_types=1);

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpenseType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('common.title'),
                'attr' => [
                    'placeholder' => $this->translator->trans('expense.title'),
                ],
            ])
            ->add('amount', NumberType::class, [
                'label' => $this->translator->trans('common.amount'),
                'scale' => 2,
                'html5' => false,
                'attr' => [
                    'placeholder' => '10.00',
                ],
                'invalid_message' => $this->translator->trans('amount.invalid'),
                'constraints' => [
                    new Assert\Positive([
                        'message' => $this->translator->trans('amount.positive'),
                    ]),
                ],
            ])
            ->add('payer', ChoiceType::class, [
                'label' => $this->translator->trans('info.paid_by'),
                'choice_label' => 'name',
                'choices' => $options['participants'],
            ])
            ->add('date', DateType::class, [
                'label' => $this->translator->trans('common.date'),
                'data' => new \DateTime(),
            ])
            ->add('beneficiaries', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'by_reference' => false,
                'label' => false,
                'choice_label' => false,
                'choices' => $options['participants'],
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => $this->translator->trans('beneficiary.min'),
                    ]),
                ],
            ]);
        $builder
            ->get('amount')->addModelTransformer(new CallbackTransformer(
                function ($amountInCents) {
                    if (null === $amountInCents) {
                        return null;
                    }
                    if (!is_int($amountInCents)) {
                        throw new \UnexpectedValueException(sprintf('Expected integer for amount in cents, got %s', get_debug_type($amountInCents)));
                    }

                    return $amountInCents / 100;
                },
                function ($amountInEuros) {
                    if (null === $amountInEuros) {
                        return null;
                    }
                    if (!is_numeric($amountInEuros)) {
                        throw new \UnexpectedValueException(sprintf('Expected numeric value for amount in euros, got %s', get_debug_type($amountInEuros)));
                    }

                    return (int) round($amountInEuros * 100);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'participants' => [],
        ]);
    }
}
