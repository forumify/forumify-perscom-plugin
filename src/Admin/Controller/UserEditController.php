<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\UserData;
use Forumify\PerscomPlugin\Admin\Form\UserType;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\SyncUserService;
use Perscom\Http\Resources\Users\CoverPhotoResource;
use Perscom\Http\Resources\Users\ProfilePhotoResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('perscom-io.admin.users.manage')]
class UserEditController extends AbstractController
{
    public function __construct(private readonly string $tempUploadDir)
    {
    }

    public function __invoke(PerscomFactory $perscomFactory, SyncUserService $syncUserService, Request $request, int $id): Response
    {
        $perscom = $perscomFactory->getPerscom(true);
        $user = $perscom
            ->users()
            ->get($id, [
                'secondary_assignment_records',
                'secondary_assignment_records.status',
                'secondary_assignment_records.unit',
                'secondary_assignment_records.position',
                'secondary_assignment_records.specialty',
                'fields',
            ])
            ->json('data');

        $data = UserData::fromArray($user);
        $oldSecondaryAssignments = $data->getSecondaryAssignments();

        $form = $this->createForm(UserType::class, $data, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserData $data */
            $data = $form->getData();

            $updatedData = $data->toUpdateArray($user);
            if (!empty($updatedData)) {
                $perscom->users()->update($id, $updatedData);
            }

            $this->handleFileUpload($data->getSignature(), $perscom->users()->profile_photo($id));
            $this->handleFileUpload($data->getUniform(), $perscom->users()->cover_photo($id));

            $this->handleSecondaryAssignmentRemovals(
                $perscom,
                $user['id'],
                $oldSecondaryAssignments,
                $data->getSecondaryAssignments()
            );

            $syncUserService->syncFromPerscom($id);

            $this->addFlash('success', 'perscom.admin.users.edit.saved');
            return $this->redirectToRoute('perscom_admin_user_edit', ['id' => $id]);
        }

        return $this->render('@ForumifyPerscomPlugin/admin/users/edit/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    private function handleFileUpload(?UploadedFile $uploadedFile, CoverPhotoResource|ProfilePhotoResource $resource): void
    {
        if ($uploadedFile === null) {
            return;
        }

        $ext = $uploadedFile->guessExtension() ?? $uploadedFile->getClientOriginalExtension();
        $filename = uniqid('user-resource-', false) . '.' . $ext;

        $file = $uploadedFile->move($this->tempUploadDir, $filename);
        $resource->create($file->getPathname());

        $fs = new Filesystem();
        $fs->remove($file->getPathname());
    }

    private function handleSecondaryAssignmentRemovals(
        Perscom $perscom,
        int $userId,
        array $oldAssignments,
        array $newAssignments
    ): void {
        foreach ($oldAssignments as $recordId) {
            if (!in_array((string)$recordId, $newAssignments, true)) {
                $perscom->users()
                    ->assignment_records($userId)
                    ->delete($recordId);
            }
        }
    }
}
