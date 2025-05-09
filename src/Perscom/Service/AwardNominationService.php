<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;

class AwardNominationService
{
    public function __construct(
        private readonly AwardNominationRepository $awardNominationRepository
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
            'status' => 3
        ]);

        if (!empty($existing)) {
            throw new AfterActionReportAlreadyExistsException();
        }
    }

    public function update(AwardNomination $nomination): void
    {
        $this->awardNominationRepository->save($nomination);
    }
}
