<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserListController extends AbstractController
{
    #[Route('/users', 'user_list')]
    public function __invoke(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/users/list/list.html.twig');
    }
}
