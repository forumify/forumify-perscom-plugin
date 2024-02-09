<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\AwardType;
use Forumify\PerscomPlugin\Admin\Form\QualificationType;
use Forumify\PerscomPlugin\Admin\Form\RankType;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $user = $perscom->users()->get($id)->json('data');
        if ($user === null) {
            $this->addFlash('error', 'perscom.admin.users.not_found');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('text', TextareaType::class);
        $this->addTypeFields($type, $formBuilder);

        $form = $formBuilder->getForm();
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

    private function addTypeFields(string $type, FormBuilderInterface $formBuilder): void
    {
        switch ($type) {
            case 'award':
                $formBuilder->add('award_id', AwardType::class, ['label' => 'Award']);
                break;
            case 'rank':
                $formBuilder
                    ->add('type', ChoiceType::class, [
                        'choices' => [
                            'Promote' => 0,
                            'Demote' => 1,
                        ],
                    ])
                    ->add('rank_id', RankType::class, ['label' => 'Rank']);
                break;
            case 'assignment':
                // TODO: yeah..
                break;
            case 'qualification':
                $formBuilder->add('qualification_id', QualificationType::class, ['label' => 'Qualification']);
                break;
            default:
        }
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
