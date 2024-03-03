<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class BookController extends AbstractBaseController
{
    public function index(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $search = $data['search'] ?? '';

        $books = $entityManager->getRepository(Book::class)->findByName($search);

        return $this->respond($books);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        if (empty($data['authors'])) {
            return $this->respondError("Authors data is missing or not in the correct format.", 422);
        }

        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->respond($book);
        }

        return $this->getErrorsFromForm($form);
    }


    public function show($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        return $this->respond($book);
    }

    /**
     * @throws \Exception
     */
    public function update($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return $this->respondError('book not found!');
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(BookType::class, $book);

        if (!isset($data['authors']) || !is_array($data['authors'])) {
            return $this->json("Authors data is missing or not in the correct format.");
        }

        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->respond($book);
        }

        return $this->getErrorsFromForm($form);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function delete($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return $this->respondError('book not found!');
        }
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->respond('delete record successfully!');
    }
}
