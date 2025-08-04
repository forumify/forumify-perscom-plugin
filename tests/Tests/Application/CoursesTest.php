<?php

declare(strict_types=1);

namespace PluginTests\Application;

use DateInterval;
use DateTimeImmutable;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\PerscomPlugin\Perscom\Repository\QualificationRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ServiceRecordRepository;
use PluginTests\Application\PerscomWebTestCase;
use PluginTests\Factories\Perscom\Record\QualificationRecordFactory;
use PluginTests\Factories\Perscom\UserFactory;
use PluginTests\Factories\Stories\MilsimStory;
use PluginTests\Traits\SessionTrait;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class CoursesTest extends PerscomWebTestCase
{
    use InteractsWithLiveComponents;
    use SessionTrait;

    public function testCourseToClassReport(): void
    {
        $perscomUser = UserFactory::createOne([
            'rank' => MilsimStory::rankPFC(),
            'user' => $this->user,
        ]);

        $c = $this->client->request('GET', '/admin/perscom/courses');
        $this->client->click($c->filter('a[aria-label="New Course"]')->link());

        $this->client->submitForm('Save', [
            'course[title]' => 'Combat Life Saver Course',
            'course[description]' => '<p>Learn about being a CLS!</p>',
            'course[minimumRank]' => MilsimStory::rankPFC()->getId(),
            'course[prerequisites]' => [MilsimStory::qualificationLandNav()->getId()],
            'course[qualifications]' => [MilsimStory::qualificationCLS()->getId()],
        ]);
        // ACLs
        $this->client->submitForm('Save');

        $this->initializeSession();
        $c = $this
            ->createLiveComponent('Perscom\\CourseList', ['expanded' => false])
            ->actingAs($this->user)
            ->render()
            ->crawler()
        ;

        $link = $c->filter('.topic-link')->first();
        self::assertStringContainsString('Combat Life Saver Course', $link->siblings()->first()->innerText());

        $this->client->request('GET', $link->attr('href'));
        self::assertAnySelectorTextContains('.rich-text p', 'Learn about being a CLS!');

        $now = new DateTimeImmutable();
        $signupFrom = $now->sub(new DateInterval('PT1H'));
        $signupUntil = $now->add(new DateInterval('PT1H'));
        $start = $now->add(new DateInterval('PT2H'));
        $end = $now->add(new DateInterval('PT4H'));

        $this->client->clickLink('New Class');
        $this->client->submitForm('Save', [
            'course_class[title]' => 'CLS 001',
            'course_class[description]' => '<p>Test class.</p>',
            'course_class[signupFrom]' => $signupFrom->format('Y-m-d\TH:i:s'),
            'course_class[signupUntil]' => $signupUntil->format('Y-m-d\TH:i:s'),
            'course_class[start]' => $start->format('Y-m-d\TH:i:s'),
            'course_class[end]' => $end->format('Y-m-d\TH:i:s'),
        ]);

        $classId = $this->client->getRequest()->attributes->get('id');
        $class = self::getContainer()->get(CourseClassRepository::class)->find($classId);
        self::assertNotNull($class);

        $classComponent = $this
            ->createLiveComponent('Perscom\\CourseClassView', ['class' => $class])
            ->actingAs($this->user)
        ;
        self::assertStringContainsString('Prerequisites not met', (string)$classComponent->render());

        QualificationRecordFactory::createOne([
            'qualification' => MilsimStory::qualificationLandNav(),
            'user' => $perscomUser,
        ]);

        $render = $this
            ->createLiveComponent('Perscom\\CourseClassView', ['class' => $class])
            ->actingAs($this->user)
            ->render()
            ->toString()
        ;
        self::assertStringContainsString('Register as Student', $render);
        self::assertStringContainsString('Register as Instructor', $render);

        $render = $this
            ->createLiveComponent('Perscom\\CourseClassView', ['class' => $class])
            ->actingAs($this->user)
            ->call('toggleStudent')
            ->call('registerInstructor')
            ->render()
            ->toString()
        ;
        self::assertStringContainsString('Deregister as Student', $render);
        self::assertStringContainsString('Deregister as Instructor', $render);

        $this->client->clickLink('Submit Class Report');
        $this->client->submitForm('Save', [
            'class_result[instructors][0][present]' => true,
            'class_result[students][0][result]' => 'passed',
            'class_result[students][0][qualifications]' => [MilsimStory::qualificationCLS()->getId()],
        ]);

        self::assertResponseIsSuccessful();

        $qualificationRecords = self::getContainer()->get(QualificationRecordRepository::class)->findBy(['user' => $perscomUser->getId()]);
        $realQualifications = array_map(fn ($record) => $record->getQualification()->getName(), $qualificationRecords);
        foreach (['Land Navigation', 'Combat Life Saver'] as $expected) {
            self::assertContains($expected, $realQualifications);
        }

        $serviceRecords = self::getContainer()->get(ServiceRecordRepository::class)->findBy(['user' => $perscomUser->getId()]);
        $realServiceRecords = array_map(fn ($record) => $record->getText(), $serviceRecords);
        foreach (['Attended CLS 001', 'Graduated CLS 001'] as $expected) {
            self::assertContains($expected, $realServiceRecords);
        }
    }
}
