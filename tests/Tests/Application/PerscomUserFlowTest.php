<?php

declare(strict_types=1);

namespace PluginTests\Application;

use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\UserTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class PerscomUserFlowTest extends WebTestCase
{
    use Factories;
    use UserTrait;

    public function testEnlist(): void
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
    }
}
