<?php

declare(strict_types=1);

namespace PluginTests\Unit\Perscom\Sync\Scheduler;

use DateInterval;
use DateTime;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomSyncResultRepository;
use Forumify\PerscomPlugin\Perscom\Sync\Scheduler\PruneResultsTask;
use PluginTests\Factories\Perscom\SyncResultFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class PruneResultsTaskTest extends KernelTestCase
{
    use Factories;

    public function testPruneTasks(): void
    {
        $oldSuccessResult = SyncResultFactory::createOne([
            'end' => new DateTime()->sub(new DateInterval('P30D'))->add(new DateInterval('PT5M')),
            'start' => new DateTime()->sub(new DateInterval('P30D')),
            'success' => 1,
        ]);

        $newSuccessResult = SyncResultFactory::createOne([
            'end' => new DateTime()->sub(new DateInterval('P3D'))->add(new DateInterval('PT5M')),
            'start' => new DateTime()->sub(new DateInterval('P3D')),
            'success' => 1,
        ]);

        $oldPendingResult = SyncResultFactory::createOne([
            'start' => new DateTime()->sub(new DateInterval('P3D')),
        ]);

        $newPendingResult = SyncResultFactory::createOne([
            'start' => new DateTime()->sub(new DateInterval('PT1M')),
        ]);

        /** @var PerscomSyncResultRepository $resultRepo */
        $resultRepo = self::getContainer()->get(PerscomSyncResultRepository::class);
        $resultCount = $resultRepo->count([]);
        self::assertEquals(4, $resultCount);

        $taskPruner = self::getContainer()->get(PruneResultsTask::class);
        ($taskPruner)();

        $results = $resultRepo->findAll();
        self::assertCount(2, $results);

        $existingIds = [];
        foreach ($results as $result) {
            $existingIds[] = $result->getId();
        }

        self::assertNotContains($oldSuccessResult->getId(), $existingIds);
        self::assertContains($newSuccessResult->getId(), $existingIds);
        self::assertNotContains($oldPendingResult->getId(), $existingIds);
        self::assertContains($newPendingResult->getId(), $existingIds);
    }
}
