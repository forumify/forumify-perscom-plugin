<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use DateTime;
use Forumify\PerscomPlugin\Admin\Form\RecordType;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Exception\PerscomUserNotFoundException;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecordFormController extends AbstractController
{
    public function __construct(
        private readonly RecordService $recordService,
        private readonly PerscomUserRepository $perscomUserRepository,
    ) {
    }

    #[Route('/users/create-record/{type}', 'record_form')]
    public function __invoke(
        Request $request,
        string $type
    ): Response {
        $this->denyAccessUnlessGranted("perscom-io.admin.records.{$type}_records.create");

        $data = ['created_at' => new DateTime()];

        $userIds = $request->get('users', '');
        $userIds = array_filter(explode(',', $userIds));
        if (!empty($userIds)) {
            $data['users'] = $this->perscomUserRepository->findBy(['id' => $userIds]);
        }

        $form = $this->createForm(RecordType::class, $data, ['type' => $type]);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('@ForumifyPerscomPlugin/admin/users/record_form.html.twig', [
                'form' => $form->createView(),
                'type' => $type,
            ]);
        }

        $data = $form->getData();

        try {
            $this->recordService->createRecord($type, $data);
        } catch (PerscomUserNotFoundException) {
            $this->addFlash('error', 'perscom.admin.requires_perscom_account');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        $this->addFlash('success', 'perscom.admin.users.record_form.created');
        return $this->redirectToRoute('perscom_admin_user_list');
    }
}
