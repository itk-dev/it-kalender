<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\ICS\ICSHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
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
        yield TextField::new('name', new TranslatableMessage('Name'));
        yield UrlField::new('icsUrl', new TranslatableMessage('ICS URL'))
            ->setFormTypeOption('help',
                new TranslatableMessage('See <a href="https://github.com/itk-dev/it-kalender/blob/develop/docs/UserGuide.md#getting-ics-url">Getting ICS URL</a> for details on how to get this.')
            )
        ;
        yield AssociationField::new('calendars', new TranslatableMessage('Calendars'))
            ->hideOnForm();

        yield DateTimeField::new('icsReadAt', new TranslatableMessage('ICS read at'))
            ->hideWhenCreating()
            ->setFormTypeOption('disabled', 'disabled')
        ;
        yield DateTimeField::new('createdAt', new TranslatableMessage('Created at'))
            ->hideOnForm();
        yield DateTimeField::new('updatedAt', new TranslatableMessage('Updated at'))
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('Person'))
            ->setEntityLabelInPlural(new TranslatableMessage('People'))
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->readICS($entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->readICS($entityInstance);
    }

    private function readICS(Person $person)
    {
        try {
            $this->icsHelper->readICS($person);
            $this->addFlash('success', sprintf('ICS for %s read at %s', $person->getName(), $person->getIcsReadAt()->format(\DateTimeImmutable::ATOM)));
        } catch (\Exception $exception) {
            $this->addFlash('danger', sprintf('Error reading ICS for %s: %s', $person->getName(), $exception->getMessage()));
        }
    }
}
