<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\UserTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class EnlistmentTest extends WebTestCase
{
    use Factories;
    use UserTrait;

    public function testEnlistNewUser(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        MilsimStory::load();

        $user = $this->createAdmin();
        $client->loginUser($user);

        $client->request('GET', '/');
        $client->clickLink('Enlist');

        self::assertAnySelectorTextContains('div.rich-text', 'Enlistment Instructions');

        $client->submitForm('Enlist', [
            'enlistment[additionalFormData][reason]' => 'I am pro gamer!',
            'enlistment[firstName]' => 'John',
            'enlistment[lastName]' => 'Doe',
        ]);

        self::assertAnySelectorTextSame('div.rich-text', 'Enlistment Success');

        $perscomUser = self::getContainer()->get(PerscomUserRepository::class)->findOneBy(['user' => $user]);
        self::assertNotNull($perscomUser);

        $submission = self::getContainer()->get(FormSubmissionRepository::class)->findOneBy([
            'form' => MilsimStory::formEnlistment()->getId(),
            'user' => $perscomUser,
        ]);
        self::assertNotNull($submission);

        $client->request('GET', '/perscom/enlist');
        self::assertAnySelectorTextContains('p', 'Your enlistment is being processed');
        self::assertAnySelectorTextSame('a', 'View enlistment topic');
        self::assertAnySelectorTextSame('a', 'Start another enlistment');
    }

    public function testEnlistExistingUser(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        MilsimStory::load();

        $user = $this->createAdmin();
        $client->loginUser($user);

        UserFactory::createOne([
            'status' => MilsimStory::statusRetired(),
            'user' => $user,
        ]);

        $client->request('GET', '/');
        $client->clickLink('Enlist');
        $client->submitForm('Enlist', [
            'enlistment[additionalFormData][reason]' => 'I am pro gamer!',
            'enlistment[firstName]' => 'John',
            'enlistment[lastName]' => 'Doe',
        ]);

        self::assertAnySelectorTextSame('div.rich-text', 'Enlistment Success');
    }
}
