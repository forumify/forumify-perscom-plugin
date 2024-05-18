<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\RecordType;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecordFormController extends AbstractController
{
    #[Route('/users/create-record/{type}', 'record_form')]
    public function __invoke(
        PerscomFactory $perscomFactory,
        PerscomUserService $perscomUserService,
        RecordService $recordService,
        Request $request,
        string $type
    ): Response {
        $author = $perscomUserService->getLoggedInPerscomUser();
        if ($author === null) {
            $this->addFlash('error', 'perscom.admin.requires_perscom_account');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        $data = ['author_id' => $author['id']];

        $userIds = $request->get('users', '');
        $userIds = array_filter(explode(',', $userIds));
        $data['users'] = $userIds;

        $form = $this->createForm(RecordType::class, $data, ['type' => $type]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $recordService->createRecord($type, $data);

            $this->addFlash('success', 'perscom.admin.users.record_form.created');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/users/record_form.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
