<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\ExpenseCollection;
use Domain\Model\Expense;
use Domain\ReadModel\Member;

class ExpenseRepository implements ExpenseCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Expense $expense): void
    {
        $this->em->persist($expense);
    }

    public function remove(Expense $expense): void
    {
        $this->em->remove($expense);
    }

    public function findOneById(int $id, Member $member): ?Expense
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Expense::class, 'e')
            ->leftJoin('e.group', 'g')
            ->leftJoin('g.participants', 'p')
            ->where('e.id = :id')
            ->andWhere('p.id = :participantId')
            ->setParameter('id', $id)
            ->setParameter('participantId', $member->participant->id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Expense ? $result : null;
    }
}
