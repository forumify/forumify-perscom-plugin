<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Service\ReportInService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[AsLiveComponent('Perscom\\ReportInButton', '@ForumifyPerscomPlugin/frontend/components/report_in_button.html.twig')]
class ReportInButton extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public bool $reportedIn = false;

    public function __construct(private readonly ReportInService $reportInService)
    {
    }

    public function canReportIn(): bool
    {
        return $this->reportInService->canReportIn();
    }

    #[LiveAction]
    public function reportIn(): Response|null
    {
        $this->reportedIn = true;

        $changedStatus = $this->reportInService->reportIn();
        if ($changedStatus) {
            return $this->redirectToRoute('perscom_operations_center');
        }

        return null;
    }
}
