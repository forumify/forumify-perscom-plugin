<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\UserTrait;
use Zenstruck\Foundry\Test\Factories;

class RankRecordTest extends PerscomWebTestCase
{
    use Factories;
    use UserTrait;

    public function testCreateRankRecord(): void
    {
        $targetUser = UserFactory::createOne();

        $c = $this->client->request('GET', '/admin/perscom/records/rank');
        $newRecordLink = $c->filter('a[aria-label="New rank record"]')->link();
        $this->client->click($newRecordLink);

        $this->client->submitForm('Save', [
            'record[users]' => [$targetUser->getId()],
            'record[type]' => 'promotion',
            'record[rank]' => MilsimStory::rankPVT()->getId(),
        ]);

        self::assertResponseIsSuccessful();

        /** @var PerscomUser $perscomUser */
        $perscomUser = self::getContainer()->get(PerscomUserRepository::class)->find($targetUser->getId());
        self::assertEquals('Private Trainee', $perscomUser->getRank()->getName());
    }
}
