<?php

declare(strict_types=1);

namespace Infrastructure\Twig;

use Domain\ReadModel\Group;
use Domain\ReadModel\Participant;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('format_amount', [$this, 'formatAmount']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('participant_balance', [$this, 'getParticipantBalanceFromGroup']),
        ];
    }

    public function formatAmount(int $amount): string
    {
        $value = abs($amount / 100);
        $decimals = (0 === $amount % 100) ? 0 : 2;
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request ? $request->getLocale() : 'fr_FR';
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);

        return $formatter->formatCurrency($value, 'EUR');
    }

    public function getParticipantBalanceFromGroup(Participant $participant, Group $group): int
    {
        $result = 0;
        foreach ($group->getDebts() as $debt) {
            $result += $debt->getParticipantAmount($participant);
        }

        return $result;
    }
}
