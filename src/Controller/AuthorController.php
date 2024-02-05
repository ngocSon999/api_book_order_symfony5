<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AuthorController extends AbstractBaseController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->respond($authors);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->submit($data);
        if ($form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->respond($author);
        }

        return $this->getErrorsFromForm($form);
    }
}
