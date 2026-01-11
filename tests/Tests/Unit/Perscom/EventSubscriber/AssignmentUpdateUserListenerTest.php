<?php

declare(strict_types=1);

namespace PluginTests\Unit\Perscom\EventSubscriber;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class AssignmentUpdateUserListenerTest extends KernelTestCase
{
    use Factories;

    public function testPrePersist(): void
    {
        MilsimStory::load();

        $targetPerscomUser = UserFactory::createOne();

        $record = new AssignmentRecord();
        $record->setUser($targetPerscomUser);
        $record->setType('primary');
        $record->setStatus(MilsimStory::statusActiveDuty());
        $record->setUnit(MilsimStory::unitFirstSquad());
        $record->setPosition(MilsimStory::positionRiflemanAT());
        $record->setSpecialty(MilsimStory::specialtyInfantry());

        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist($record);
        $em->flush();

        self::assertNotNull($targetPerscomUser->getStatus());
        self::assertNotNull($targetPerscomUser->getUnit());
        self::assertNotNull($targetPerscomUser->getPosition());
        self::assertNotNull($targetPerscomUser->getSpecialty());
    }

    public function testPreRemove(): void
    {
        MilsimStory::load();

        $targetPerscomUser = UserFactory::createOne();

        // First we assign to 1st squad
        $record = new AssignmentRecord();
        $record->setCreatedAt(new DateTime('yesterday'));
        $record->setUser($targetPerscomUser);
        $record->setType('primary');
        $record->setUnit(MilsimStory::unitFirstSquad());

        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist($record);

        self::assertEquals(MilsimStory::unitFirstSquad()->getId(), $targetPerscomUser->getUnit()->getId());

        // Then we assign to second squad
        $record = new AssignmentRecord();
        $record->setUser($targetPerscomUser);
        $record->setType('primary');
        $record->setUnit(MilsimStory::unitSecondSquad());

        $em->persist($record);
        $em->flush();

        self::assertEquals(MilsimStory::unitSecondSquad()->getId(), $targetPerscomUser->getUnit()->getId());

        // Then we remove the last assignment record, which should put the user back in 1st squad
        $em->remove($record);
        $em->flush();

        self::assertEquals(MilsimStory::unitFirstSquad()->getId(), $targetPerscomUser->getUnit()->getId());
    }
}
