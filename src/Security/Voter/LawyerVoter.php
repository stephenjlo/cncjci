<?php
namespace App\Security\Voter;

use App\Entity\Lawyer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LawyerVoter extends Voter
{
    public const EDIT = 'LAWYER_EDIT';
    public const VIEW = 'LAWYER_VIEW';
    public const DELETE = 'LAWYER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Lawyer;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Lawyer $lawyer */
        $lawyer = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($lawyer, $user),
            self::EDIT => $this->canEdit($lawyer, $user),
            self::DELETE => $this->canDelete($lawyer, $user),
            default => false,
        };
    }

    private function canView(Lawyer $lawyer, User $user): bool
    {
        // Tout le monde peut voir
        return true;
    }

    private function canEdit(Lawyer $lawyer, User $user): bool
    {
        // SUPER_ADMIN peut tout modifier
        if ($user->isSuperAdmin()) {
            return true;
        }

        // RESPO_CABINET peut modifier les lawyers de son cabinet
        if ($user->isRespoCabinet()) {
            $userCabinet = $user->getCabinet();
            $lawyerCabinet = $lawyer->getCabinet();

            return $userCabinet && $lawyerCabinet
                && $userCabinet->getId() === $lawyerCabinet->getId();
        }

        // LAWYER peut seulement modifier son propre profil
        if ($user->isLawyer()) {
            $userLawyer = $user->getLawyer();
            return $userLawyer && $userLawyer->getId() === $lawyer->getId();
        }

        return false;
    }

    private function canDelete(Lawyer $lawyer, User $user): bool
    {
        // Seul SUPER_ADMIN peut supprimer
        if ($user->isSuperAdmin()) {
            return true;
        }

        // RESPO_CABINET peut désactiver (pas supprimer) les lawyers de son cabinet
        if ($user->isRespoCabinet()) {
            $userCabinet = $user->getCabinet();
            $lawyerCabinet = $lawyer->getCabinet();

            return $userCabinet && $lawyerCabinet
                && $userCabinet->getId() === $lawyerCabinet->getId();
        }

        return false;
    }
}