<?php

declare(strict_types=1);

namespace PluginTests\Application;

use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use PluginTests\Factories\Perscom\FormFieldFactory;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\UserTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class FormSubmissionTest extends WebTestCase
{
    use Factories;
    use UserTrait;

    public function testFormSubmission(): void
    {
        $client = self::createClient();
        $client->followRedirects();

        MilsimStory::load();

        $user = $this->createAdmin();
        $client->loginUser($user);

        UserFactory::createOne(['user' => $user, 'status' => MilsimStory::statusActiveDuty()]);

        $c = $client->request('GET', '/admin/perscom/forms');
        $newFormLink = $c->filter('a[aria-label="New form"]')->link();
        $client->click($newFormLink);

        $client->submitForm('Save', [
            'form[name]' => 'Leave Of Absence',
            'form[defaultStatus]' => MilsimStory::statusPending()->getId(),
            'form[description]' => 'Form description',
            'form[instructions]' => '<p>Form instructions</p>',
            'form[successMessage]' => '<p>Form success message</p>',
        ]);
        // Fields aren't manageable on forumify, so we have to create them programatically
        $form = self::getContainer()->get(FormRepository::class)->findOneBy(['name' => 'Leave Of Absence']);
        FormFieldFactory::createOne([
            'key' => 'reason',
            'label' => 'Why do you want to take time off?',
            'form' => $form,
        ]);

        $c = $client->request('GET', '/perscom/operations-center');
        $formLinks = $c->filter('a[href^="/perscom/form/"]');
        self::assertCount(1, $formLinks);

        $client->click($formLinks->first()->link());
        self::assertAnySelectorTextContains('.rich-text', 'Form instructions');

        $client->submitForm('Save', ['perscom_form[reason]' => 'Need to go to a wedding.']);
        self::assertResponseIsSuccessful();

        $c = $client->request('GET', '/admin/perscom/submissions?form=' . $form->getId());
        self::assertAnySelectorTextContains('td > span', 'Pending');

        $viewBtn = $c->filter('tbody > tr')->filter('a')->first()->link();
        $client->click($viewBtn);

        $client->submitForm('Save', ['submission_status[status]' => MilsimStory::statusApproved()->getId()]);
        self::assertAnySelectorTextContains('span', 'Approved');
        self::assertAnySelectorTextContains('h4', 'Approved');
    }
}
