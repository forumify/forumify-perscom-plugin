<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\MenuBuilder\MenuType;

use Forumify\Core\Entity\MenuItem;
use Forumify\Core\MenuBuilder\MenuType\AbstractMenuType;
use Forumify\PerscomPlugin\Perscom\Service\PerscomEnlistService;
use Twig\Environment;

class PerscomMenuType extends AbstractMenuType
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PerscomEnlistService $perscomEnlistService
    ) {
    }

    public function getType(): string
    {
        return 'perscom';
    }

    protected function render(MenuItem $item): string
    {
        return $this->twig->render('@ForumifyPerscomPlugin/frontend/menu/perscom.html.twig', [
            'canEnlist' => $this->perscomEnlistService->canEnlist(),
            'item' => $item,
        ]);
    }

    public function getPayloadFormType(): ?string
    {
        return PerscomMenuFormType::class;
    }
}
