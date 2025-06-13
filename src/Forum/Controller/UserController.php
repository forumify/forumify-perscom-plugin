<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly RankRecordRepository $rankRecordRepository,
        private readonly AwardRecordRepository $awardRecordRepository,
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
        private readonly PerscomFactory $perscomFactory,
        private readonly UserRepository $userRepository,
        private readonly ReportInRepository $reportInRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('user/{id<\d+>}', 'user')]
    public function __invoke(PerscomUser $user): Response
    {
        $lastReportInDate = $this
            ->reportInRepository
            ->findOneBy(['perscomUserId' => $user->getPerscomId()])
            ?->getLastReportInDate()
        ;

        return $this->render('@ForumifyPerscomPlugin/frontend/user/user.html.twig', [
            'awards' => $this->getAwardCounts($user),
            'user' => $user,
            'tis' => $this->getTimeInService($user),
            'tig' => $this->getTimeInGrade($user),
            'reportInDate' => $lastReportInDate,
            'secondaryAssignments' => [],
        ]);
    }

    private function getTimeInGrade(PerscomUser $user): ?DateInterval
    {
        $lastRankRecordDate = $this->rankRecordRepository
            ->createQueryBuilder('rr')
            ->select('MAX(rr.createdAt)')
            ->where('rr.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;

        $lastRankRecord = reset($lastRankRecordDate);
        if ($lastRankRecord === false) {
            return null;
        }

        return (new DateTime(reset($lastRankRecord)))->diff(new DateTime());
    }

    private function getTimeInService(PerscomUser $user): ?DateInterval
    {
        $firstAssignmentRecord = $this->assignmentRecordRepository
            ->createQueryBuilder('ar')
            ->select('MIN(ar.createdAt)')
            ->where('ar.user = :user')
            ->andWhere('ar.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'primary')
            ->getQuery()
            ->getResult()
        ;

        $firstAssignmentDate = reset($firstAssignmentRecord);
        if ($firstAssignmentDate === false) {
            return null;
        }

        return (new DateTime(reset($firstAssignmentDate)))->diff(new DateTime());
    }

    private function getAwardCounts(PerscomUser $user): array
    {
        $awardCounts = $this->awardRecordRepository
            ->createQueryBuilder('ar')
            ->join('ar.award', 'a')
            ->select('a.id, COUNT(a.id) AS count, a.name, a.image')
            ->where('ar.user = :user')
            ->groupBy('a.id')
            ->orderBy('a.position', 'ASC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult()
        ;
        return$awardCounts;
    }

    private function transformSecondaryAssignments(array $user): array
    {
        $records = $user['secondary_assignment_records'] ?? [];

        $grouped = [];
        foreach ($records as $record) {
            $unitId = $record['unit']['id'] ?? null;
            if ($unitId === null) {
                continue;
            }

            if (!isset($grouped[$unitId])) {
                $grouped[$unitId] = $record['unit'];
            }

            $data = [
                $record['position']['name'] ?? '',
                $record['speciality']['name'] ?? '',
                $record['status']['name'] ?? '',
            ];

            $grouped[$unitId]['records'][] = implode(' | ', array_filter($data));
        }

        return $grouped;
    }

    private function transformAwards(array $user): array
    {
        $awards = [];
        foreach ($user['award_records'] ?? [] as $record) {
            $award = $record['award'] ?? null;
            if ($award === null) {
                continue;
            }

            $awardId = $award['id'];
            if (!isset($awards[$awardId])) {
                $awards[$awardId] = $award;
                $awards[$awardId]['count'] = 0;
            }
            $awards[$awardId]['count']++;
        }

        uasort($awards, fn (array $a, array $b) => $a['order'] - $b['order']);
        return $awards;
    }
}
