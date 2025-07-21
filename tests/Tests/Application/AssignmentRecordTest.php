<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;

class AssignmentRecordTest extends PerscomWebTestCase
{
    public function testCreatePrimaryAssignmentRecord(): void
    {
        $targetUser = UserFactory::createOne();

        $c = $this->client->request('GET', '/admin/perscom/records/assignment');
        $newRecordLink = $c->filter('a[aria-label="New assignment record"]')->link();
        $this->client->click($newRecordLink);

        // phpcs:ignore
        $this->client->submitForm('Save', [
            'record[users]' => [$targetUser->getId()],
            'record[type]' => 'primary',
            'record[status]' => MilsimStory::statusActiveDuty()->getId(),
            'record[specialty]' => MilsimStory::specialtyInfantry()->getId(),
            'record[unit]' => MilsimStory::unitFirstSquad()->getId(),
            'record[position]' => MilsimStory::positionRiflemanAT()->getId(),
            'record[text]' => 'Initial assignment',
        ]);

        self::assertResponseIsSuccessful();

        /** @var PerscomUser $perscomUser */
        $perscomUser = self::getContainer()->get(PerscomUserRepository::class)->find($targetUser->getId());
        self::assertEquals('Active Duty', $perscomUser->getStatus()->getName());
        self::assertEquals('11B', $perscomUser->getSpecialty()->getAbbreviation());
        self::assertEquals('First Squad', $perscomUser->getUnit()->getName());
        self::assertEquals('Rifleman AT', $perscomUser->getPosition()->getName());
    }

    public function testCreateSecondaryAssignmentRecord(): void
    {
        $targetUser = UserFactory::createOne();

        $c = $this->client->request('GET', '/admin/perscom/records/assignment');
        $newRecordLink = $c->filter('a[aria-label="New assignment record"]')->link();
        $this->client->click($newRecordLink);

        $this->client->submitForm('Save', [
            'record[users]' => [$targetUser->getId()],
            'record[type]' => 'secondary',
            'record[status]' => MilsimStory::statusActiveDuty()->getId(),
            'record[specialty]' => MilsimStory::specialtyInfantry()->getId(),
            'record[unit]' => MilsimStory::unitFirstSquad()->getId(),
            'record[position]' => MilsimStory::positionRiflemanAT()->getId(),
            'record[text]' => 'Initial assignment',
        ]);

        self::assertResponseIsSuccessful();

        /** @var PerscomUser $perscomUser */
        $perscomUser = self::getContainer()->get(PerscomUserRepository::class)->find($targetUser->getId());
        self::assertNull($perscomUser->getStatus());
        self::assertNull($perscomUser->getPosition());
        self::assertNull($perscomUser->getSpecialty());
        self::assertNull($perscomUser->getUnit());
    }
}
