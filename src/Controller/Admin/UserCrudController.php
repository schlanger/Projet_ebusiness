<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            ChoiceField::new('roles')->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])->allowMultipleChoices(),
            TextField::new('password')->setFormType(PasswordType::class),
        ];
    }

    // Utilisez l'événement prePersist pour hasher le mot de passe avant la persistance
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    // Utilisez l'événement preUpdate pour hasher le mot de passe avant la mise à jour
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    // Méthode pour hasher le mot de passe
    private function hashPassword($entityInstance): void
    {
        if ($entityInstance instanceof User && $plainPassword = $entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
        }
    }
}
