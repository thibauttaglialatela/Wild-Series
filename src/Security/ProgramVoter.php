<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;
use App\Entity\Program;
use Symfony\Component\Security\Core\Security;

class ProgramVoter extends Voter
{
    //définitions des constantes
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Program) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        //ROLE_ADMIN can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof User) {
            //the user must be logged in; if not, deny access
            return false;
        }

        $program = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($program, $user);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canEdit(Program $program, User $user): bool
    {
        return $user === $program->getOwner();
    }
}
