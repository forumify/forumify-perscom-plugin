<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class SubmissionController extends AbstractController
{
    #[Route('/submissions', 'submission_list')]
    public function __invoke()
    {
        return $this->render('@ForumifyPerscomPlugin/admin/submissions/list/list.html.twig');
    }
}
