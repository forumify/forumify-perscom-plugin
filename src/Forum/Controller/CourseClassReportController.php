<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\ClassResultType;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomUserNotFoundException;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\PerscomPlugin\Perscom\Service\CourseClassService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/courses/class', 'course_class_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseClassReportController extends AbstractController
{
    public function __construct(
        private readonly CourseClassService $classService,
        private readonly CourseClassRepository $classRepository,
    ) {
    }

    #[Route('/{id}/report', 'report')]
    public function __invoke(CourseClass $class, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $class->getCourse(),
            'permission' => 'manage_classes',
        ]);

        $form = $this->createForm(ClassResultType::class, $class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('@ForumifyPerscomPlugin/frontend/course/class_report.html.twig', [
                'class' => $class,
                'form' => $form->createView(),
            ]);
        }

        $alreadyProcessed = $class->getResult();

        $class->setResult(true);
        $this->classRepository->save($class);

        if (!$alreadyProcessed) {
            try {
                $this->classService->processResult($class);
            } catch (PerscomUserNotFoundException) {
                $this->addFlash('error', 'perscom.admin.requires_perscom_account');
            }
        }

        return $this->redirectToRoute('perscom_course_class_view', ['id' => $class->getId()]);
    }
}
