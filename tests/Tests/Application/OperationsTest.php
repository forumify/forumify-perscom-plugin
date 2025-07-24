<?php

declare(strict_types=1);

namespace PluginTests\Application;

use DateInterval;
use DateTimeImmutable;
use Forumify\Core\Repository\SettingRepository;
use PluginTests\Factories\Forumify\CalendarFactory;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\SessionTrait;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class OperationsTest extends PerscomWebTestCase
{
    use InteractsWithLiveComponents;
    use SessionTrait;

    public function testOperationToAttendance(): void
    {
        self::getContainer()->get(SettingRepository::class)->set('perscom.operations.absent_notification', true);

        UserFactory::createOne(['user' => $this->user, 'status' => MilsimStory::statusActiveDuty()]);

        $c = $this->client->request('GET', '/admin/perscom/operations');
        $this->client->click($c->filter('a[aria-label="New Operation"]')->link());

        $this->client->submitForm('Save', [
            'operation[title]' => 'Sandstorm',
            'operation[description]' => 'Operation description',
            'operation[content]' => '<p>Operation content</p>',
            'operation[start]' => '2025-01-01',
            'operation[requestRsvp]' => true,
            'operation[missionBriefingTemplate]' => 'Briefing template',
            'operation[afterActionReportTemplate]' => 'After action report template',
        ]);
        // ACLs
        $this->client->submitForm('Save');

        $this->client->request('GET', '/perscom/operations/sandstorm');
        self::assertSelectorTextSame('h1', 'Sandstorm');

        $calendar = CalendarFactory::createOne(['title' => 'Missions']);

        $c = $this->client->clickLink('New Mission');
        $initialBriefingText = $c->filter('#mission_briefing')->innerText();
        self::assertSame('Briefing template', $initialBriefingText);

        $now = new DateTimeImmutable();
        $start = $now->add(new DateInterval('P1D'));
        $end = $now->add(new DateInterval('P1DT1H'));

        $c = $this->client->submitForm('Save', [
            'mission[title]' => 'Test Mission',
            'mission[start]' => $start->format('Y-m-d H:i:s'),
            'mission[end]' => $end->format('Y-m-d H:i:s'),
            'mission[calendar]' => $calendar->getId(),
            'mission[sendNotification]' => true,
            'mission[createCombatRecords]' => true,
            'mission[briefing]' => 'Mission briefing',
        ]);

        self::assertSelectorTextSame('h1', 'Test Mission');
        self::assertAnySelectorTextContains('button', 'RSVP');

        $c = $this->client->clickLink('New After Action Report');
        $initialAarText = $c->filter('#after_action_report_report')->innerText();
        self::assertSame('After action report template', $initialAarText);

        $unit = MilsimStory::unitFirstSquad();

        $i = 0;
        $attendances = ['present', 'present', 'present', 'excused', 'excused', 'excused', 'absent', 'absent', 'absent'];
        $attendanceJson = [];
        $present = [];
        foreach ($unit->getUsers() as $unitUser) {
            $attendance = $attendances[$i++];
            $attendanceJson[$attendance][] = $unitUser->getId();
            if ($attendance === 'present') {
                $present[] = $unitUser;
            }
        }
        self::assertCount(3, $present);

        $c = $this->client->submitForm('Save', [
            'after_action_report[unit]' => $unit->getId(),
            'after_action_report[report]' => '<span id="test-aar-report">After action report content</span>',
            'after_action_report[attendanceJson]' => json_encode($attendanceJson),
        ]);

        self::assertSelectorExists('#test-aar-report');
        foreach ($present as $presentUser) {
            $cr = $presentUser->getCombatRecords()->first();
            self::assertNotNull($cr);
            self::assertEquals('Operation Sandstorm: Mission Test Mission', $cr->getText());
        }

        $this->initializeSession();
        $c = $this
            ->createLiveComponent('Perscom\\AttendanceSheet')
            ->actingAs($this->user)
            ->submitForm([
                'form' => [
                    'from' => $now->sub(new DateInterval('P3D'))->format('Y-m-d'),
                    'to' => $now->add(new DateInterval('P3D'))->format('Y-m-d'),
                ]
            ], 'calculate')
            ->render()
            ->crawler()
        ;

        $value = $c->filter('td[data-testid="total-present"]')->siblings()->first()->innerText();
        self::assertEquals(3, $value);
        $value = $c->filter('td[data-testid="total-excused"]')->siblings()->first()->innerText();
        self::assertEquals(3, $value);
        $value = $c->filter('td[data-testid="total-absent"]')->siblings()->first()->innerText();
        self::assertEquals(3, $value);
        $value = $c->filter('td[data-testid="perc-attended"]')->siblings()->first()->innerText();
        self::assertEquals('33%', $value);
        $value = $c->filter('td[data-testid="perc-accountable"]')->siblings()->first()->innerText();
        self::assertEquals('66%', $value);
    }
}
