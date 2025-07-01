<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateInterval;
use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\AwardRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ReportInRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly RankRecordRepository $rankRecordRepository,
        private readonly AwardRecordRepository $awardRecordRepository,
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
        private readonly ReportInRepository $reportInRepository,
        private readonly PerscomUserRepository $perscomUserRepository,
    ) {
    }

    #[Route('user/{id<\d+>}', 'user')]
    public function __invoke(int $id): Response
    {
        $user = $this->perscomUserRepository->findOneByPerscomId($id);
        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $lastReportInDate = $this
            ->reportInRepository
            ->findOneBy(['perscomUserId' => $user->getPerscomId()])
            ?->getLastReportInDate()
        ;

        return $this->render('@ForumifyPerscomPlugin/frontend/user/user.html.twig', [
            'awards' => $this->getAwardCounts($user),
            'reportInDate' => $lastReportInDate,
            'secondaryAssignments' => $this->getSecondaryUnits($user),
            'tig' => $this->getTimeInGrade($user),
            'tis' => $this->getTimeInService($user),
            'user' => $user,
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
        return $this->awardRecordRepository
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
    }

    private function getSecondaryUnits(PerscomUser $user): array
    {
        /** @var array<AssignmentRecord> */
        $records = $this->assignmentRecordRepository->findBy([
            'type' => 'secondary',
            'user' => $user,
        ]);

        $grouped = [];
        foreach ($records as $record) {
            $unitId = $record->getUnit()?->getPerscomId();
            if ($unitId === null) {
                continue;
            }

            if (!isset($grouped[$unitId])) {
                $grouped[$unitId] = ['name' => $record->getUnit()->getName()];
            }

            $data = [
                $record->getPosition()?->getName(),
                $record->getSpecialty()?->getName(),
                $record->getStatus()?->getName(),
            ];

            $grouped[$unitId]['records'][] = implode(' | ', array_filter($data));
        }

        return $grouped;
    }
}
