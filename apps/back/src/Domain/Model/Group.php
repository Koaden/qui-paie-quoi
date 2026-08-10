<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\ReadModel\Debt;
use Domain\ReadModel\Participant as ReadModelParticipant;

class Group
{
    private ?int $id = null;

    private string $name;

    private ?string $description = null;

    private Member $owner;

    /** @var iterable<Participant> */
    private iterable $participants;

    /** @var iterable<Expense> */
    private iterable $expenses;

    /** @var iterable<Invitation> */
    private iterable $invitations;

    public function __construct(
        string $name,
        Member $owner,
        ?string $description = null,
    ) {
        $this->name = $name;
        $this->owner = $owner;
        $this->description = $description;
        $this->participants = [];
        $this->expenses = [];
        $this->invitations = [];
        $this->addParticipant($owner->getParticipant());
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): Member
    {
        return $this->owner;
    }

    public function setOwner(Member $owner): self
    {
        $this->addParticipant($owner->getParticipant());
        $this->owner = $owner;

        return $this;
    }

    /** @return iterable<Participant> */
    public function getParticipants(): iterable
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        $participants = iterator_to_array($this->participants);

        foreach ($participants as $p) {
            if ($p === $participant) {
                return $this;
            }
        }

        $participants[] = $participant;

        $this->participants = $participants;

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        $this->participants = array_filter(
            [...iterator_to_array($this->participants)],
            function (Participant $p) use ($participant) {
                return $p->getId() !== $participant->getId();
            }
        );

        return $this;
    }

    /** @return iterable<Expense> */
    public function getExpenses(): iterable
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        $items = iterator_to_array($this->expenses);

        foreach ($items as $item) {
            if ($item === $expense) {
                return $this;
            }
        }

        $items[] = $expense;
        $expense->setGroup($this);

        $this->expenses = $items;

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        $this->expenses = array_filter(
            [...iterator_to_array($this->expenses)],
            function (Expense $e) use ($expense) {
                return $e->getId() !== $expense->getId();
            }
        );

        return $this;
    }

    /** @return iterable<Invitation> */
    public function getInvitations(): iterable
    {
        return $this->invitations;
    }

    public function getInvitationFromParticipant(Participant $participant): ?Invitation
    {
        foreach ($this->invitations as $invitation) {
            if ($invitation->getParticipant() === $participant) {
                return $invitation;
            }
        }

        return null;
    }

    public function addInvitation(Invitation $invitation): self
    {
        $invitations = iterator_to_array($this->invitations);

        foreach ($invitations as $i) {
            if ($i === $invitation) {
                return $this;
            }
        }

        $items[] = $invitation;

        $this->invitations = $invitations;

        return $this;
    }

    public function removeInvitation(Invitation $invitation): self
    {
        $this->invitations = array_filter(
            [...iterator_to_array($this->invitations)],
            function (Invitation $i) use ($invitation) {
                return $i->getId() !== $invitation->getId();
            }
        );

        return $this;
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->expenses as $expense) {
            $total += $expense->getAmount();
        }

        return $total;
    }

    public function getNumberOfExpenses(): int
    {
        return iterator_count($this->expenses);
    }

    /**
     * @return array<Debt>
     */
    public function getDebts(): array
    {
        $debts = [];
        $balance = [];
        $creditors = [];
        $debtors = [];

        foreach ($this->getParticipants() as $participant) {
            $balance[$participant->getId()] = 0;
        }
        foreach ($this->getExpenses() as $expense) {
            foreach ($this->getParticipants() as $participant) {
                $balance[$participant->getId()] += $expense->getParticipantBalance($participant);
            }
        }
        foreach ($this->getParticipants() as $participant) {
            $participant = ReadModelParticipant::fromModel($participant);
            if ($balance[$participant->id] > 0) {
                array_push($creditors, $participant);
            } elseif ($balance[$participant->id] < 0) {
                array_push($debtors, $participant);
            }
        }
        foreach ($debtors as $debtor) {
            foreach ($creditors as $creditor) {
                $debt = min(-$balance[$debtor->id], $balance[$creditor->id]);
                if ($debt > 0) {
                    array_push($debts, new Debt($debtor, $creditor, $debt));
                    $balance[$debtor->id] += $debt;
                    $balance[$creditor->id] -= $debt;
                }
            }
        }

        return $debts;
    }
}
