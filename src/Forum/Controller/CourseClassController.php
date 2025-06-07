<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\CourseClassType;
use Forumify\PerscomPlugin\Perscom\Entity\Course;
use Forumify\PerscomPlugin\Perscom\Entity\CourseClass;
use Forumify\PerscomPlugin\Perscom\Repository\CourseClassRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/courses', 'course_class_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class CourseClassController extends AbstractController
{
    public function __construct(
        private readonly CourseClassRepository $courseClassRepository,
    ) {
    }

    #[Route('/class/{id}', 'view')]
    public function view(CourseClass $class): Response
    {
        return $this->render('@ForumifyPerscomPlugin/frontend/course/class.html.twig', [
            'class' => $class,
        ]);
    }

    #[Route('/{slug}/class/create', 'create')]
    public function create(Request $request, Course $course): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $course,
            'permission' => 'manage_classes',
        ]);

        $class = new CourseClass();
        $class->setCourse($course);

        return $this->handleClassForm($request, true, $class);
    }

    #[Route('/class/{id}/edit', 'edit')]
    public function edit(Request $request, CourseClass $class): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $class->getCourse(),
            'permission' => 'manage_classes',
        ]);

        return $this->handleClassForm($request, false, $class);
    }

    #[Route('/class/{id}/delete', 'delete')]
    public function delete(Request $request, CourseClass $class): Response
    {
        if (!$request->get('confirmed')) {
            return $this->render('@ForumifyPerscomPlugin/frontend/course/class_delete.html.twig', [
                'class' => $class,
            ]);
        }

        $courseSlug = $class->getCourse()->getSlug();
        $this->courseClassRepository->remove($class);

        $this->addFlash('success', 'perscom.course.class.deleted');
        return $this->redirectToRoute('perscom_courses_view', ['slug' => $courseSlug]);
    }

    private function handleClassForm(Request $request, bool $isNew, CourseClass $class): Response
    {
        $form = $this->createForm(CourseClassType::class, $class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $class = $form->getData();
            $this->courseClassRepository->save($class);

            $this->addFlash('success', $isNew ? 'perscom.course.class.created' : 'perscom.course.class.edited');
            return $this->redirectToRoute('perscom_course_class_view', ['id' => $class->getId()]);
        }

        return $this->render('@Forumify/form/simple_form_page.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'perscom.course.class.create' : 'perscom.course.class.edit',
            'cancelPath' => $isNew
                ? $this->generateUrl('perscom_courses_view', ['slug' => $class->getCourse()->getSlug()])
                : $this->generateUrl('perscom_course_class_view', ['id' => $class->getId()]),
        ]);
    }
}
