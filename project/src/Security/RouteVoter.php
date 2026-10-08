<?php

namespace App\Security;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\HttpFoundation\RequestStack;

class RouteVoter extends Voter
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // We only support a special attribute for dynamic route checking
        return $attribute === 'DYNAMIC_ROUTE_ACCESS';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Usuario) {
            return false;
        }

        // Always allow Admin to everything if they have ROLE_ADMIN in hierarchy
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        $customRole = $user->getCustomRole();
        if (!$customRole) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $currentRoute = $request->attributes->get('_route');

        return in_array($currentRoute, $customRole->getRoutes());
    }
}
