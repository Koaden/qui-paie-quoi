<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\MemberCollection;
use Domain\Model\Group;
use Domain\Model\Member;
use Domain\ReadModel\Member as ReadMember;

class MemberRepository implements MemberCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Member $member): void
    {
        $this->em->persist($member);
    }

    public function findOneByEmail(string $email): ?Member
    {
        $qb = $this->em->createQueryBuilder()
            ->select('m')
            ->from(Member::class, 'm')
            ->where('m.email = :email')
            ->setParameter('email', $email)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Member ? $result : null;
    }

    public function findOneById(int $id): ?Member
    {
        $qb = $this->em->createQueryBuilder()
            ->select('m')
            ->from(Member::class, 'm')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Member ? $result : null;
    }

    public function findOneGroupById(int $id, ReadMember $member): ?Group
    {
        $qb = $this->em->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g')
            ->innerJoin('Domain\Model\GroupMembership', 'gm', 'WITH', 'gm.group = g')
            ->innerJoin('gm.member', 'm')
            ->where('g.id = :id')
            ->andWhere('m = :member')
            ->setParameter('id', $id)
            ->setParameter('member', $member->id)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result instanceof Group ? $result : null;
    }

    /** @return array<Group> */
    public function findAllGroups(ReadMember $member): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g')
            ->innerJoin('Domain\Model\GroupMembership', 'gm', 'WITH', 'gm.group = g')
            ->innerJoin('gm.member', 'm')
            ->where('m = :member')
            ->setParameter('member', $member->id)
            ->getQuery();

        /** @var array<Group> */
        $result = $qb->getResult();

        return $result;
    }
}
