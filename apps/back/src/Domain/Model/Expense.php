<?php

declare(strict_types=1);

namespace Domain\Model;

class Expense
{
    private ?int $id = null;

    private string $title;

    private int $amount;

    private ?\DateTime $date = null;

    private Participant $payer;

    /** @var iterable<Participant> */
    private iterable $beneficiaries;

    private Group $group;

    private Member $creator;

    /** @param iterable<Participant> $beneficiaries */
    public function __construct(
        string $title,
        int $amount,
        Participant $payer,
        Group $group,
        Member $creator,
        ?\DateTime $date = null,
        ?iterable $beneficiaries = [],
    ) {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('The amount should be greatter than 0.');
        }
        $this->title = $title;
        $this->amount = $amount;
        $this->payer = $payer;
        $this->group = $group;
        $this->creator = $creator;
        $this->date = $date ?? new \DateTime();
        $this->beneficiaries = $beneficiaries ?? [];

        if (0 === iterator_count($this->beneficiaries)) {
            $this->beneficiaries = [$payer];
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPayer(): Participant
    {
        return $this->payer;
    }

    public function setPayer(Participant $payer): self
    {
        $this->payer = $payer;

        return $this;
    }

    /** @return iterable<Participant> */
    public function getBeneficiaries(): iterable
    {
        return $this->beneficiaries;
    }

    /** @param iterable<Participant> $beneficiaries */
    public function setBeneficiaries(iterable $beneficiaries): self
    {
        $this->beneficiaries = $beneficiaries;

        return $this;
    }

    public function addBeneficiary(Participant $beneficiariy): self
    {
        $beneficiaries = iterator_to_array($this->beneficiaries);

        foreach ($beneficiaries as $b) {
            if ($b === $beneficiariy) {
                return $this;
            }
        }

        $beneficiaries[] = $beneficiariy;

        $this->beneficiaries = $beneficiaries;

        return $this;
    }

    public function removeBeneficiary(Participant $beneficiary): self
    {
        $this->beneficiaries = array_filter(
            [...iterator_to_array($this->beneficiaries)],
            function (Participant $b) use ($beneficiary) {
                return $b->getId() !== $beneficiary->getId();
            }
        );

        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getCreator(): Member
    {
        return $this->creator;
    }

    public function setCreator(Member $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getParticipantBalance(Participant $participant): int
    {
        $result = 0;

        foreach ($this->beneficiaries as $beneficiary) {
            if ($beneficiary === $participant) {
                $result -= (int) ($this->amount / iterator_count($this->beneficiaries));
                break;
            }
        }
        if ($this->payer === $participant) {
            $result += $this->amount;
        }

        return $result;
    }
}
