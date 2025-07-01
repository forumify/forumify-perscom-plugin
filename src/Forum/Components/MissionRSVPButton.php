<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\MissionRSVPButton', '@ForumifyPerscomPlugin/frontend/components/mission_rsvp_button.html.twig')]
class MissionRSVPButton extends AbstractController
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
            'perscomUserId' => $perscomUser->getPerscomId(),
        ]);
    }

    #[LiveAction]
    public function toggle(#[LiveArg]
    bool $going): ?Response
    {
        $rsvp = $this->getRSVP() ?? $this->createMissionRSVP();
        if ($rsvp === null) {
            return null;
        }

        $rsvp->setGoing($going);
        $this->missionRSVPRepository->save($rsvp);

        return $this->redirectToRoute('perscom_missions_view', ['id' => $this->mission->getId()]);
    }

    #[LiveAction]
    public function cancel(): Response
    {
        $rsvp = $this->getRSVP();
        if ($rsvp !== null) {
            $this->missionRSVPRepository->remove($rsvp);
        }

        return $this->redirectToRoute('perscom_missions_view', ['id' => $this->mission->getId()]);
    }

    private function createMissionRSVP(): ?MissionRSVP
    {
        $perscomUser = $this->perscomUserService->getLoggedInPerscomUser();
        if ($perscomUser === null) {
            return null;
        }

        $rsvp = new MissionRSVP();
        $rsvp->setMission($this->mission);
        $rsvp->setPerscomUserId($perscomUser->getPerscomId());

        return $rsvp;
    }
}
