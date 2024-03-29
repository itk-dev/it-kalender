<?php

namespace App\Controller\Admin;

use App\EasyAdminHelper;
use App\Entity\Calendar;
use App\Field\VichImageField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', new TranslatableMessage('Name'));
        yield TextField::new('slug', new TranslatableMessage('Slug'))
            ->setHelp(new TranslatableMessage('Slug can contain only letters, digits and dashes (-).'));

        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true)) {
            $entity = $this->getContext()->getEntity()->getInstance();
            $attr = EasyAdminHelper::getFileInputAttributes($entity, 'logoFile');

            yield VichImageField::new('logoFile', new TranslatableMessage('Logo file'))
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $attr);
        } else {
            yield VichImageField::new('logo', new TranslatableMessage('Logo'));
        }

        yield AssociationField::new('people', new TranslatableMessage('People'));
        yield DateTimeField::new('createdAt', new TranslatableMessage('Created at'))
            ->hideOnForm();
        yield DateTimeField::new('updatedAt', new TranslatableMessage('Updated at'))
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('Calendar'))
            ->setEntityLabelInPlural(new TranslatableMessage('Calendars'))
            ->showEntityActionsInlined()
            ->setSearchFields(['name', 'slug']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(
                Crud::PAGE_INDEX,
                Action::new('show', 'Show')
                    ->linkToUrl(fn (Calendar $calendar) => $this->generateUrl(
                        'calendar_show', [
                            'slug' => $calendar->getSlug(),
                        ]
                    ))
            )
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action): Action {
                return $action->displayIf(static function (Calendar $person) {
                    return $person->getPeople()->isEmpty();
                });
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DELETE, Action::EDIT]);
    }

    /**
     * @param Calendar $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        assert($entityInstance instanceof Calendar);
        $this->addFlash('success', new TranslatableMessage('Calendar {name} created', ['name' => $entityInstance->getName()]));
    }

    /**
     * @param Calendar $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        assert($entityInstance instanceof Calendar);
        $this->addFlash('success', new TranslatableMessage('Calendar {name} updated', ['name' => $entityInstance->getName()]));
    }
}
