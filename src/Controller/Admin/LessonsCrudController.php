<?php

namespace App\Controller\Admin;

use App\Entity\Lessons;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

/**
 * Handles the CRUD operations for the Lessons entity in the admin panel.
 * 
 * This controller configures how the Lessons entity is displayed, edited, and managed in the EasyAdmin interface.
 */
class LessonsCrudController extends AbstractCrudController
{
    /**
     * Returns the fully qualified class name (FQCN) of the Lessons entity.
     * 
     * @return string The entity class name (Lessons::class).
     */
    public static function getEntityFqcn(): string
    {
        return Lessons::class;
    }

    /**
     * Configures the fields that will be displayed on the CRUD pages (list, edit, create) for the Lessons entity.
     * 
     * @param string $pageName The current page name (list, detail, edit, create).
     * 
     * @return iterable An array of fields to be displayed on the page.
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            // ID field, hidden on the form page
            IdField::new('id')->hideOnForm(),
            
            // Title field for the "title" attribute
            TextField::new('title'),
            
            // URL field for the "videourl" attribute (could be used to store the video URL)
            TextField::new('videourl'),
            
            // Content field for the "content" attribute (description or additional details about the lesson)
            TextField::new('content'),
            
            // Money field for the "price" attribute, displayed with currency EUR
            MoneyField::new('price', 'Price')
                ->setCurrency('EUR')
                ->setStoredAsCents(false), 
            
            // Association field for the "formation" relationship (many-to-one with the Formation entity)
            AssociationField::new('formation'),
        ];
    }
}
