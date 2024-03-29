<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\ICS\ICSHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Translation\TranslatableMessage;

class PersonCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ICSHelper $icsHelper
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', new TranslatableMessage('Name'))
            ->addCssClass('align-text-top');

        yield UrlField::new('icsUrl', new TranslatableMessage('ICS URL'))
            ->setFormType(TextareaType::class)
            ->setFormTypeOptions([
                'help' => new TranslatableMessage('See <a href="{url}">Getting ICS URL</a> for details on how to get this.', [
                    '{url}' => $this->getParameter('url_getting_ics_url'),
                ]),
            ])
            ->addCssClass('align-text-top');

        yield AssociationField::new('calendars', new TranslatableMessage('Calendars'))
            ->setTemplatePath('admin/person/calendars.html.twig')
            ->setTextAlign(TextAlign::LEFT)
            ->hideOnForm()
            ->addCssClass('align-text-top');

        yield DateTimeField::new('icsReadAt', new TranslatableMessage('ICS read at'))
            ->hideWhenCreating()
            ->setFormTypeOption('disabled', 'disabled')
            ->addCssClass('align-text-top');

        yield DateTimeField::new('createdAt', new TranslatableMessage('Created at'))
            ->hideOnForm()
            ->addCssClass('align-text-top');

        yield DateTimeField::new('updatedAt', new TranslatableMessage('Updated at'))
            ->hideOnForm()
            ->addCssClass('align-text-top');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('Person'))
            ->setEntityLabelInPlural(new TranslatableMessage('People'))
            ->showEntityActionsInlined()
            ->setSearchFields(['name']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action): Action {
                return $action->displayIf(static function (Person $person) {
                    return $person->getCalendars()->isEmpty();
                });
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DELETE, Action::EDIT]);
    }

    /**
     * @param Person $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        assert($entityInstance instanceof Person);
        $this->addFlash('success', new TranslatableMessage('Person {name} created', ['name' => $entityInstance->getName()]));

        $this->readICS($entityInstance);
    }

    /**
     * @param Person $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        assert($entityInstance instanceof Person);
        $this->addFlash('success', new TranslatableMessage('Person {name} updated', ['name' => $entityInstance->getName()]));

        $this->readICS($entityInstance);
    }

    private function readICS(Person $person): void
    {
        try {
            if ($this->icsHelper->readICS($person)) {
                $this->addFlash('success', new TranslatableMessage('ICS for {name} read at {read_at}', ['name' => $person->getName(), 'read_at' => $person->getIcsReadAt()->format(\DateTimeImmutable::ATOM)]));
            } elseif ($readAt = $person->getIcsReadAt()) {
                $this->addFlash('warning', new TranslatableMessage('ICS for {name} not changed since last read at {read_at}', ['name' => $person->getName(), 'read_at' => $person->getIcsReadAt()->format(\DateTimeImmutable::ATOM)]));
            } else {
                $this->addFlash('warning', new TranslatableMessage('ICS for {name} not changed since last read', ['name' => $person->getName()]));
            }
        } catch (\Exception $exception) {
            $this->addFlash('danger', new TranslatableMessage('Error reading ICS for {name}: {message}', ['name' => $person->getName(), 'message' => $exception->getMessage()]));
        }
    }
}
