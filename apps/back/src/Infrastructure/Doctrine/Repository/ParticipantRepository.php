<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\ParticipantCollection;
use Domain\Model\Participant;

class ParticipantRepository implements ParticipantCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Participant $participant): void
    {
        $this->em->persist($participant);
    }

    public function remove(Participant $participant): void
    {
        $this->em->remove($participant);
    }

    public function findOneById(int $id): ?Participant
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Participant::class, 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Participant ? $result : null;
    }
}
