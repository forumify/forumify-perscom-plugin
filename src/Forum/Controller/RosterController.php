<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RosterController extends AbstractController
{
    #[Route('/roster', 'roster')]
    public function __invoke(PerscomFactory $perscomFactory): Response
    {
        $perscom = $perscomFactory->getPerscom();
        $groups = $perscom
            ->groups()
            ->all(['units', 'units.users', 'units.users.rank', 'units.users.status'])
            ->json('data') ?? [];

        return $this->render('@ForumifyPerscomPlugin/frontend/roster/roster.html.twig', [
            'groups' => $groups
        ]);
    }
}
