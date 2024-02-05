<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class BookController extends AbstractBaseController
{
    public function index(EntityManagerInterface $entityManager): Response
    {
        $books = $entityManager->getRepository(Book::class)->findall();

        return $this->respond($books);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        if (!isset($data['authors']) || !is_array($data['authors'])) {
            return $this->json("Authors data is missing or not in the correct format.");
        }
        $form->submit($data);

        if ($form->isValid()) {
            $authorIds = $data['authors'];
            foreach ($authorIds as $authorId) {
                $author = $entityManager->getRepository(Author::class)->find($authorId);
                if ($author) {
                    $book->addAuthor($author);
                }
            }
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
            foreach ($book->getAuthors() as $author) {
                $book->removeAuthor($author);
            }

            $authorIds = $data['authors'];
            foreach ($authorIds as $authorId) {
                $author = $entityManager->getRepository(Author::class)->find($authorId);
                if ($author) {
                    $book->addAuthor($author);
                }
            }
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
