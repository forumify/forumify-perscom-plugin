<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;
use Forumify\PerscomPlugin\Perscom\Exception\AwardNominationAlreadyExistsException;

class AwardNominationService
{
    public function __construct(
        private readonly AwardNominationRepository $awardNominationRepository,
        private readonly SettingRepository $settingRepository
    ) {
    }

    public function create(AwardNomination $nomination): void
    {
        $this->ensureNotDuplicate($nomination);

        $this->awardNominationRepository->save($nomination);
    }

    private function ensureNotDuplicate(AwardNomination $nomination): void
    {
        $existing = $this->awardNominationRepository->findBy([
            'perscomUserId' => $nomination->getPerscomUserId(),
            'receiverUserId' => $nomination->getReceiverUserId(),
            'awardId' => $nomination->getAwardId(),
            'status' => $this->settingRepository->get('perscom.award_nominations.pending_status_id')
        ]);

        if (!empty($existing)) {
            throw new AwardNominationAlreadyExistsException();
        }
    }

    public function update(AwardNomination $nomination): void
    {
        $this->awardNominationRepository->save($nomination);
    }
}
