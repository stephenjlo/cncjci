<?php
namespace App\Security\Voter;

use App\Entity\Cabinet;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CabinetVoter extends Voter
{
    public const EDIT = 'CABINET_EDIT';
    public const VIEW = 'CABINET_VIEW';
    public const DELETE = 'CABINET_DELETE';
    public const MANAGE_LAWYERS = 'CABINET_MANAGE_LAWYERS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::MANAGE_LAWYERS])
            && $subject instanceof Cabinet;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Cabinet $cabinet */
        $cabinet = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($cabinet, $user),
            self::EDIT => $this->canEdit($cabinet, $user),
            self::DELETE => $this->canDelete($cabinet, $user),
            self::MANAGE_LAWYERS => $this->canManageLawyers($cabinet, $user),
            default => false,
        };
    }

    private function canView(Cabinet $cabinet, User $user): bool
    {
        return true;
    }

    private function canEdit(Cabinet $cabinet, User $user): bool
    {
        // SUPER_ADMIN peut tout modifier
        if ($user->isSuperAdmin()) {
            return true;
        }

        // RESPO_CABINET peut modifier son propre cabinet
        if ($user->isRespoCabinet()) {
            $userCabinet = $user->getCabinet();
            return $userCabinet && $userCabinet->getId() === $cabinet->getId();
        }

        return false;
    }

    private function canDelete(Cabinet $cabinet, User $user): bool
    {
        // Seul SUPER_ADMIN peut supprimer/désactiver
        return $user->isSuperAdmin();
    }

    private function canManageLawyers(Cabinet $cabinet, User $user): bool
    {
        // SUPER_ADMIN peut gérer tous les lawyers
        if ($user->isSuperAdmin()) {
            return true;
        }

        // RESPO_CABINET peut gérer les lawyers de son cabinet
        if ($user->isRespoCabinet()) {
            $userCabinet = $user->getCabinet();
            return $userCabinet && $userCabinet->getId() === $cabinet->getId();
        }

        return false;
    }
}