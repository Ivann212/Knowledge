<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

/**
 * Handles the CRUD operations for the User entity in the admin panel.
 * 
 * This controller configures how the User entity is displayed, edited, and managed in the EasyAdmin interface.
 */
class UserCrudController extends AbstractCrudController
{
    /**
     * Returns the fully qualified class name (FQCN) of the User entity.
     * 
     * @return string The entity class name (User::class).
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configures the fields that will be displayed on the CRUD pages (list, edit, create) for the User entity.
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
            
            // Email field for the "email" attribute (the user's email address)
            TextField::new('email'),
            
            // Roles field to display the user's roles (array of roles)
            ArrayField::new('roles'),
            
            // Boolean field for the "isVerified" attribute, indicating if the user is verified
            BooleanField::new('isVerified'),
        ];
    }
}
