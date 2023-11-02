<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RosterController extends AbstractController
{
    #[Route('/roster', 'roster')]
    public function __invoke(): Response
    {
        return new Response('ok');
    }
}
