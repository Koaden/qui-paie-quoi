<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Collection\GroupCollection;
use Domain\Model\Group;

class GroupRepository implements GroupCollection
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(Group $group): void
    {
        $this->em->persist($group);
    }
}
