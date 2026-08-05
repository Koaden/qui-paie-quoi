<?php

declare(strict_types=1);

namespace Infrastructure\Doctrine\DataFixtures;

use Application\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Domain\Enum\GroupRole;
use Domain\Model\Expense;
use Domain\Model\Group;
use Domain\Model\GroupMembership;
use Domain\Model\Member;
use Domain\Model\Participant;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $a = new Member('alice@test.com', 'Alice');
        $hashedPassword = $this->hasher->hashPassword(new User($a), 'password');
        $a->setPassword($hashedPassword);

        $b = new Participant('Bob');
        $c = new Participant('Charlie');
        $d = new Participant('Diana');

        $group = new Group('Test Group', $a, 'This is a test group. Lorem ipsum dolor sit amet consectetur adipiscing elit.');
        $membership = new GroupMembership($a, GroupRole::OWNER, $group);

        $group->addParticipant($b);
        $group->addParticipant($c);
        $group->addParticipant($d);

        $group->addExpense(new Expense(
            'Resto', 20000, $a->getParticipant(), $group, $a, null, new ArrayCollection([$a->getParticipant(), $b, $c, $d])
        ));

        $group->addExpense(new Expense(
            'Cadeau Bob', 12000, $c, $group, $a, null, new ArrayCollection([$a->getParticipant(), $c, $d])
        ));

        $group->addExpense(new Expense(
            'Apéro', 2000, $d, $group, $a, null, new ArrayCollection([$d])
        ));

        $group->addExpense(new Expense(
            'Place de ciné Charlie', 1600, $d, $group, $a, null, new ArrayCollection([$c])
        ));

        $group->addExpense(new Expense(
            'Boisson Alice', 1000, $a->getParticipant(), $group, $a, null, new ArrayCollection([$a->getParticipant()])
        ));

        $manager->persist($group);
        $manager->persist($membership);

        $manager->flush();
    }
}
