<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\FormFieldType;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormField;
use Forumify\PerscomPlugin\Perscom\Repository\FormRepository;
use Forumify\PerscomPlugin\Perscom\Sync\Service\SyncService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/forms/{formId}/fields', 'form_field')]
#[IsGranted('perscom-io.admin.organization.forms.manage')]
class FormFieldController extends AbstractCrudController
{
    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/forms/field_list.html.twig';
    protected string $formTemplate = '@ForumifyPerscomPlugin/admin/forms/field_form.html.twig';
    protected string $deleteTemplate = '@ForumifyPerscomPlugin/admin/forms/field_delete.html.twig';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FormRepository $formRepository,
        private readonly SyncService $syncService,
    ) {
    }

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return FormField::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomFormFieldTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        if ($data === null) {
            $data = new FormField();
            $data->setForm($this->getParent());
        }
        return $this->createForm(FormFieldType::class, $data);
    }

    protected function templateParams(array $params = []): array
    {
        $syncEnabled = $this->syncService->isSyncEnabled();
        $params = parent::templateParams([
            'parentForm' => $this->getParent(),
            'syncEnabled' => $syncEnabled,
            ...$params,
        ]);

        if ($syncEnabled) {
            $params['capabilities'] = ['create' => false, 'edit' => false, 'delete' => false];
        }

        return $params;
    }

    protected function redirectAfterSave(mixed $entity, bool $isNew): Response
    {
        return $this->redirectToRoute($this->getRoute('list'), ['formId' => $this->getParent()->getId()]);
    }

    private function getParent(): Form
    {
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->formRepository->find($request->attributes->get('formId'));
        if ($form === null) {
            throw $this->createNotFoundException();
        }

        return $form;
    }

    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        $parameters['formId'] = $this->getParent()->getId();
        return parent::redirectToRoute($route, $parameters, $status);
    }
}
