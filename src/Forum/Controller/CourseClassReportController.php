<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\CourseClassResultType;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClassResult;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassResultRepository;
use Forumify\PerscomPlugin\Perscom\Service\ClassResultService;
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
        private readonly CourseClassResultRepository $courseClassResultRepository,
        private readonly ClassResultService $classResultService,
    ) {
    }

    #[Route('/{id}/report', 'report')]
    public function __invoke(CourseClass $class, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $class->getCourse(),
            'permission' => 'manage_classes',
        ]);

        $result = new CourseClassResult();
        $result->setClass($class);

        $form = $this->createForm(CourseClassResultType::class, $result, ['class' => $class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();

            $this->courseClassResultRepository->save($result);
            $this->classResultService->processResult($result);

            return $this->redirectToRoute('perscom_course_class_view', ['id' => $class->getId()]);
        }

        return $this->render('@Forumify/form/simple_form_page.html.twig', [
            'form' => $form->createView(),
            'title' => 'perscom.course.class.create_report',
            'cancelPath' => $this->generateUrl('perscom_course_class_view', ['id' => $class->getId()]),
        ]);
    }
}
