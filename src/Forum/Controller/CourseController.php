<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[Route('/courses', 'courses_')]
class CourseController extends AbstractController
{
    #[Route('/', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/frontend/course/list.html.twig');
    }

    #[Route('/{slug}', 'view')]
    public function view(Course $course): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $course,
            'permission' => 'view',
        ]);

        return $this->render('@ForumifyPerscomPlugin/frontend/course/course.html.twig', [
            'course' => $course,
        ]);
    }
}
