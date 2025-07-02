<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller\Records;

use DateTime;
use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\RecordType;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use stdClass;
use Symfony\Component\Form\FormInterface;

abstract class AbstractRecordCrudController extends AbstractCrudController
{
    protected bool $allowEdit = false;

    public function __construct(private readonly RecordService $recordService)
    {
    }

    abstract protected function getRecordType(): string;

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getForm(?object $data): FormInterface
    {
        $data = ['created_at' => new DateTime()];
        return $this->createForm(RecordType::class, $data, [
            'type' => $this->getRecordType(),
        ]);
    }

    protected function save(bool $isNew, FormInterface $form): object
    {
        $recordData = $form->getData();
        $this->recordService->createRecord($this->getRecordType(), $recordData);

        return new stdClass();
    }
}
