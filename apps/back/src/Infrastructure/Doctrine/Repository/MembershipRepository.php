<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\MembershipCollection;
use Domain\Model\GroupMembership;

class MembershipRepository implements MembershipCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(GroupMembership $membership): void
    {
        $this->em->persist($membership);
    }

    public function findOneById(int $id): ?GroupMembership
    {
        $qb = $this->em->createQueryBuilder()
            ->select('gm')
            ->from(GroupMembership::class, 'gm')
            ->where('gm.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof GroupMembership ? $result : null;
    }
}
