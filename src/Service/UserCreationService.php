<?php
namespace App\Service;

use App\Entity\Lawyer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreationService
{
    private const DEFAULT_PASSWORD = 'ChangeMe2024!';

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailService $emailService
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

        // Mot de passe par défaut
        $hashedPassword = $this->passwordHasher->hashPassword($user, self::DEFAULT_PASSWORD);
        $user->setPassword($hashedPassword);

        // Forcer le changement de mot de passe à la première connexion
        $user->setMustChangePassword(true);

        // Si le lawyer a un cabinet, associer le User au cabinet
        if ($lawyer->getCabinet()) {
            $user->setCabinet($lawyer->getCabinet());
        }

        $this->em->persist($user);
        $this->em->flush();

        // Envoyer l'email de bienvenue
        $this->emailService->sendWelcomeEmail(
            $email,
            $lawyer->getFullName(),
            'ROLE_LAWYER',
            self::DEFAULT_PASSWORD,
            $lawyer->getCabinet()?->getName()
        );

        return $user;
    }

    /**
     * Met à jour le rôle d'un User en RESPO_CABINET et envoie un email de notification
     */
    public function promoteToRespoCabinet(User $user): void
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_RESPO_CABINET', $roles)) {
            $roles[] = 'ROLE_RESPO_CABINET';
            $user->setRoles($roles);
            $this->em->flush();

            // Envoyer l'email de promotion
            if ($user->getCabinet()) {
                $this->emailService->sendPromotionEmail(
                    $user->getEmail(),
                    $user->getFullName(),
                    $user->getCabinet()->getName()
                );
            }
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
