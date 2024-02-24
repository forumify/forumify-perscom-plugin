<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\RecordType;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecordFormController extends AbstractController
{
    #[Route('/users/{id}/create-record/{type}', 'record_form')]
    public function __invoke(
        PerscomFactory $perscomFactory,
        PerscomUserService $perscomUserService,
        Request $request,
        int $id,
        string $type
    ): Response {
        $author = $perscomUserService->getLoggedInPerscomUser();
        if ($author === null) {
            $this->addFlash('error', 'perscom.admin.requires_perscom_account');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        $perscom = $perscomFactory->getPerscom();
        $user = $perscom
            ->users()
            ->get($id)
            ->json('data');

        if ($user === null) {
            $this->addFlash('error', 'perscom.admin.users.not_found');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        $form = $this->createForm(RecordType::class, null, ['type' => $type]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['user_id'] = $id;
            $data['author_id'] = $author['id'];

            $this->saveRecord($perscom, $type, $data);

            $this->addFlash('success', 'perscom.admin.users.record_form.created');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/users/record_form.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
            'user' => $user,
        ]);
    }

    private function saveRecord(Perscom $perscom, string $type, array $data): void
    {
        $userResource = $perscom->users();
        $recordResource = match ($type) {
            'service' => $userResource->service_records(...),
            'award' => $userResource->award_records(...),
            'combat' => $userResource->combat_records(...),
            'rank' => $userResource->rank_records(...),
            'assignment' => $userResource->assignment_records(...),
            'qualification' => $userResource->qualification_records(...),
        };

        $recordResource($data['user_id'])->create($data);
    }
}
