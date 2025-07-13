<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\UserTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class RankRecordTest extends WebTestCase
{
    use Factories;
    use UserTrait;

    public function testCreateRankRecord(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        MilsimStory::load();

        $user = $this->createAdmin();
        $client->loginUser($user);

        $targetUser = UserFactory::createOne();

        $c = $client->request('GET', '/admin/perscom/records/rank');
        $newRecordLink = $c->filter('a[aria-label="New rank record"]')->link();
        $client->click($newRecordLink);

        // phpcs:ignore
        $client->submitForm('Save', [
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
