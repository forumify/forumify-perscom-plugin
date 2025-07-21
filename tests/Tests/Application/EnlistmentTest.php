<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Repository\FormSubmissionRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;

class EnlistmentTest extends PerscomWebTestCase
{
    public function testEnlistNewUser(): void
    {
        $this->client->request('GET', '/');
        $this->client->clickLink('Enlist');

        self::assertAnySelectorTextContains('div.rich-text', 'Enlistment Instructions');

        $this->client->submitForm('Enlist', [
            'enlistment[additionalFormData][reason]' => 'I am pro gamer!',
            'enlistment[firstName]' => 'John',
            'enlistment[lastName]' => 'Doe',
        ]);

        self::assertAnySelectorTextSame('div.rich-text', 'Enlistment Success');

        $perscomUser = self::getContainer()->get(PerscomUserRepository::class)->findOneBy(['user' => $this->user]);
        self::assertNotNull($perscomUser);

        $submission = self::getContainer()->get(FormSubmissionRepository::class)->findOneBy([
            'form' => MilsimStory::formEnlistment()->getId(),
            'user' => $perscomUser,
        ]);
        self::assertNotNull($submission);

        $this->client->request('GET', '/perscom/enlist');
        self::assertAnySelectorTextContains('p', 'Your enlistment is being processed');
        self::assertAnySelectorTextSame('a', 'View enlistment topic');
        self::assertAnySelectorTextSame('a', 'Start another enlistment');
    }

    public function testEnlistExistingUser(): void
    {
        UserFactory::createOne([
            'status' => MilsimStory::statusRetired(),
            'user' => $this->user,
        ]);

        $this->client->request('GET', '/');
        $this->client->clickLink('Enlist');
        $this->client->clickLink('Start another enlistment');
        $this->client->submitForm('Enlist', [
            'enlistment[additionalFormData][reason]' => 'I am pro gamer!',
            'enlistment[firstName]' => 'John',
            'enlistment[lastName]' => 'Doe',
        ]);

        self::assertAnySelectorTextSame('div.rich-text', 'Enlistment Success');
    }
}
