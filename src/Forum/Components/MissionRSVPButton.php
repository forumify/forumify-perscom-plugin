<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\MissionRSVPButton', '@ForumifyPerscomPlugin/frontend/components/mission_rsvp_button.html.twig')]
class MissionRSVPButton
{
    use DefaultActionTrait;

    #[LiveProp]
    public Mission $mission;

    public function __construct(
        private readonly PerscomUserService $perscomUserService,
        private readonly MissionRSVPRepository $missionRSVPRepository,
    ) {
    }

    public function getRSVP(): ?MissionRSVP
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return null;
        }

        return $this->missionRSVPRepository->findOneBy([
            'mission' => $this->mission,
            'perscomUserId' => $perscomUser['id'],
        ]);
    }

    #[LiveAction]
    public function toggle(#[LiveArg] bool $going): void
    {
        $rsvp = $this->getRSVP() ?? $this->createMissionRSVP();
        if ($rsvp === null) {
            return;
        }

        $rsvp->setGoing($going);
        $this->missionRSVPRepository->save($rsvp);
    }

    #[LiveAction]
    public function cancel(): void
    {
        $rsvp = $this->getRSVP();
        if ($rsvp !== null) {
            $this->missionRSVPRepository->remove($rsvp);
        }
    }

    private function createMissionRSVP(): ?MissionRSVP
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return null;
        }

        $rsvp = new MissionRSVP();
        $rsvp->setMission($this->mission);
        $rsvp->setPerscomUserId($perscomUser['id']);

        return $rsvp;
    }
}
