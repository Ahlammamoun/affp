<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'email']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // ❌ NE PAS ajouter EDIT sur DETAIL : déjà présent par défaut
            // ->add(Crud::PAGE_DETAIL, Action::EDIT)
            // ✅ si tu veux customiser le bouton existant :
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier')->setIcon('fa fa-pen');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->onlyOnIndex();
        $email = EmailField::new('email');

        $roles = ArrayField::new('roles')
            ->setHelp('Ex: ["ROLE_USER"] ou ["ROLE_USER","ROLE_ADMIN"]');

        $premiumUntil = DateTimeField::new('premiumUntil')
            ->setHelp('Si la date est dans le futur => premium actif.');

        $plainPassword = TextField::new('plainPassword', 'Mot de passe')
            ->setHelp('Laisse vide pour ne pas changer.')
            ->setFormTypeOption('mapped', false)
            ->setFormTypeOption('required', $pageName === Crud::PAGE_NEW)
            ->onlyOnForms();

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $email, $roles, $premiumUntil];
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            return [$id, $email, $roles, $premiumUntil];
        }

        return [$email, $roles, $premiumUntil, $plainPassword];
    }

    private function getPlainPasswordFromRequest(): string
    {
        $req = $this->getContext()?->getRequest();
        if (!$req) return '';

        // EasyAdmin poste un tableau de formulaire avec une racine (souvent "User" mais pas garanti).
        // On cherche plainPassword dans toutes les racines :
        $all = $req->request->all();
        foreach ($all as $root) {
            if (is_array($root) && array_key_exists('plainPassword', $root)) {
                return (string) ($root['plainPassword'] ?? '');
            }
        }

        return '';
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        $plain = trim($this->getPlainPasswordFromRequest());
        if ($plain !== '') {
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $plain)
            );
        }

        // si roles vide -> ROLE_USER
        if (!$entityInstance->getRoles()) {
            $entityInstance->setRoles(['ROLE_USER']);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        $plain = trim($this->getPlainPasswordFromRequest());
        if ($plain !== '') {
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $plain)
            );
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
