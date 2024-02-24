<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateTime;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[Route('user/{id<\d+>}', 'user')]
    public function __invoke(
        int $id,
        PerscomFactory $perscomFactory,
        UserRepository $userRepository,
        Request $request,
        TranslatorInterface $translator,
    ): Response {
        try {
            $user = $perscomFactory->getPerscom()
                ->users()
                ->get($id, [
                    'rank',
                    'rank.image',
                    'status',
                    'unit',
                    'specialty',
                    'position',
                    'service_records',
                    'award_records',
                    'rank_records',
                    'rank_records.rank',
                    'rank_records.rank.image',
                    'combat_records',
                    'assignment_records',
                    'assignment_records.position',
                    'assignment_records.unit',
                    'assignment_records.status',
                    'qualification_records',
                    'qualification_records.qualification',
                    'secondary_assignment_records.unit',
                    'secondary_assignment_records.position',
                    'secondary_assignment_records.specialty',
                    'secondary_assignment_records',
                ])
                ->json('data');
        } catch (NotFoundException) {
            throw new NotFoundHttpException($translator->trans('perscom.user.not_found'));
        }

        $now = new DateTime();
        $tis = (new DateTime($user['created_at']))->diff($now);

        $lastRankRecord = reset($user['rank_records']);
        $tig = $lastRankRecord !== false ? (new DateTime($lastRankRecord['created_at']))->diff($now) : null;

        $secondaryAssignments = $this->transformSecondaryAssignments($user);

        return $this->render('@ForumifyPerscomPlugin/frontend/user/user.html.twig', [
            'forumAccount' => $userRepository->findOneBy(['email' => $user['email']]),
            'secondaryAssignments' => $secondaryAssignments,
            'user' => $user,
            'tis' => $tis,
            'tig' => $tig,
        ]);
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
}
