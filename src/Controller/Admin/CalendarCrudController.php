<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Form\CalendarPersonType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield CollectionField::new('calendarPeople')
            ->renderExpanded()
            ->setEntryIsComplex()
            ->setEntryType(CalendarPersonType::class);
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('show', 'Show')
                    ->linkToUrl(fn (Calendar $calendar) => $this->generateUrl(
                        'calendar_show', [
                            'id' => $calendar->getId()
                        ]
                    )
                )
            );
    }
}
