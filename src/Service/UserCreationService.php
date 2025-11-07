<?php
namespace App\Service;

use App\Entity\Lawyer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Crée automatiquement un compte User pour un Lawyer
     * avec un mot de passe par défaut à changer à la première connexion
     */
    public function createUserForLawyer(Lawyer $lawyer): ?User
    {
        // Vérifier qu'il a au moins un email
        $primaryEmail = $lawyer->getPrimaryEmail();
        $email = $primaryEmail ? $primaryEmail->getEmail() : $lawyer->getEmail();

        if (!$email) {
            // Pas d'email, impossible de créer le compte
            return null;
        }

        // Vérifier qu'un User avec cet email n'existe pas déjà
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser) {
            // Lier le lawyer au User existant si pas déjà fait
            if (!$existingUser->getLawyer()) {
                $existingUser->setLawyer($lawyer);
                $this->em->flush();
            }
            return $existingUser;
        }

        // Créer le nouveau User
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($lawyer->getFirstName());
        $user->setLastName($lawyer->getLastName());
        $user->setRoles(['ROLE_LAWYER']);
        $user->setLawyer($lawyer);
        $user->setIsActive(true);

        // Mot de passe par défaut : "ChangeMe2024!" (à forcer le changement)
        $defaultPassword = 'ChangeMe2024!';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $defaultPassword);
        $user->setPassword($hashedPassword);

        // Forcer le changement de mot de passe à la première connexion
        $user->setMustChangePassword(true);

        // Si le lawyer a un cabinet, associer le User au cabinet
        if ($lawyer->getCabinet()) {
            $user->setCabinet($lawyer->getCabinet());
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Met à jour le rôle d'un User en RESPO_CABINET
     */
    public function promoteToRespoCabinet(User $user): void
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_RESPO_CABINET', $roles)) {
            $roles[] = 'ROLE_RESPO_CABINET';
            $user->setRoles($roles);
            $this->em->flush();
        }
    }

    /**
     * Vérifie si un User doit changer son mot de passe
     */
    public function mustChangePassword(User $user): bool
    {
        return $user->mustChangePassword();
    }
}
