<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\InvitationCollection;
use Domain\Model\Invitation;

class InvitationRepository implements InvitationCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Invitation $invitation): void
    {
        $this->em->persist($invitation);
    }

    public function remove(Invitation $invitation): void
    {
        $this->em->remove($invitation);
    }

    public function findOneById(int $id): ?Invitation
    {
        $qb = $this->em->createQueryBuilder()
            ->select('i')
            ->from(Invitation::class, 'i')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Invitation ? $result : null;
    }

    public function findOneByCode(string $code): ?Invitation
    {
        $qb = $this->em->createQueryBuilder()
            ->select('i')
            ->from(Invitation::class, 'i')
            ->where('i.code = :code')
            ->setParameter('code', $code)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Invitation ? $result : null;
    }
}
