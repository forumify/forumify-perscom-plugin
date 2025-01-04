<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Automation\Trigger;

use Forumify\Automation\Repository\AutomationRepository;
use Forumify\Automation\Scheduler\AutomationScheduler;
use Forumify\Automation\Trigger\TriggerInterface;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(method: 'trigger')]
class UserEnlistedTrigger implements TriggerInterface
{
    public function __construct(
        private readonly AutomationRepository $automationRepository,
        private readonly AutomationScheduler $automationScheduler,
    ) {
    }

    public static function getType(): string
    {
        return 'PERSCOM: User Enlisted';
    }

    public function getPayloadFormType(): ?string
    {
        return null;
    }

    public function trigger(UserEnlistedEvent $event): void
    {
        $automations = $this->automationRepository->findByTriggerType(self::getType());
        foreach ($automations as $automation) {
            $this->automationScheduler->schedule($automation, [
                'submission' => $event->submission,
                'user' => $event->perscomUser,
            ]);
        }
    }
}

