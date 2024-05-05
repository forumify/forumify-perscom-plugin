<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class RosterController extends AbstractController
{
    #[Route('/roster', 'roster')]
    public function __invoke(PerscomFactory $perscomFactory, Stopwatch $stopwatch): Response
    {
        $groups = $perscomFactory
            ->getPerscom()
            ->groups()
            ->all()
            ->json('data') ?? [];

        return $this->render('@ForumifyPerscomPlugin/frontend/roster/roster.html.twig', [
            'groups' => $groups
        ]);
    }
}
