<?php

namespace App\Controller\Admin;

use App\Entity\Formations;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

/**
 * Handles the CRUD operations for the Formations entity in the admin panel.
 * 
 * This controller configures how the Formations entity is displayed, edited, and managed in the EasyAdmin interface.
 */
class FormationsCrudController extends AbstractCrudController
{
    /**
     * Returns the fully qualified class name (FQCN) of the Formations entity.
     * 
     * @return string The entity class name (Formations::class).
     */
    public static function getEntityFqcn(): string
    {
        return Formations::class;
    }

    /**
     * Configures the fields that will be displayed on the CRUD pages (list, edit, create) for the Formations entity.
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
            TextField::new('title', 'Title'),
            
            // Association field for the "theme" relationship (many-to-one)
            AssociationField::new('theme', 'Theme'),
            
            // Money field for the "price" attribute, displayed with currency EUR
            MoneyField::new('price', 'Price')
                ->setCurrency('EUR')
                ->setStoredAsCents(false), 
            
            // Image field for uploading and displaying an image
            ImageField::new('image', 'Image')
                ->setUploadDir('public/images')  // Directory where the image will be uploaded
        ];
    }
}
