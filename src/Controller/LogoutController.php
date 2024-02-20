<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class LogoutController extends AbstractBaseController
{
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;


    public function __construct(
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var UserInterface|null $user */
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if ($user instanceof UserInterface) {
            $this->tokenStorage->setToken(null);
        }

        return $this->json(['message' => 'Logout successful'], Response::HTTP_OK);
    }
}
