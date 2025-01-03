<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Security;

use Exception;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser;
use Shopsys\FrameworkBundle\Model\Security\Exception\LoginFailedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class Authenticator
{
    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Bundle\SecurityBundle\Security $security
     */
    public function __construct(
        protected readonly TokenStorageInterface $tokenStorage,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly ?Security $security = null,
    ) {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function checkLoginProcess(Request $request)
    {
        $error = null;

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $session = $request->getSession();
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        if ($error !== null) {
            throw new LoginFailedException(
                'Log in failed.',
                $error instanceof Exception ? $error : null,
            );
        }

        return true;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function loginUser(CustomerUser $customerUser, Request $request)
    {
        $token = new UsernamePasswordToken(
            $customerUser,
            'frontend',
            $customerUser->getRoles(),
        );
        $this->tokenStorage->setToken($token);

        // dispatch the login event
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);

        $request->getSession()->migrate();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function loginAdministrator(Administrator $administrator)
    {
        $redirectResponse = $this->security->login($administrator, 'security.authenticator.form_login.administration');

        return $redirectResponse;
    }
}
