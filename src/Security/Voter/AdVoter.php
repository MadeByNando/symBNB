<?php

namespace App\Security\Voter;

use App\Entity\Ad;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // Check if attribute is 'edit' or 'view' and if the second parameter is an instance of Ad entity
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Ad;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }        

        // you know $subject is an Ad object, thanks to supports
        /** @var Ad $ad */
        $ad = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                // return true or false
                return $this->canView($ad, $user);
                break;
            case self::EDIT:
                // return true or false
                return $this->canEdit($ad, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
        return false;
    }

    private function canView(Ad $ad, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($ad, $user)) {
            return true;
        }

        // the Ad object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        // return !$ad->isPrivate();

        return true;
    }

    private function canEdit(Ad $ad, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $ad->getAuthor();
    }
}
