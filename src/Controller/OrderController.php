<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractBaseController
{
    public function index(EntityManagerInterface $entityManager): Response
    {
        $orders = $entityManager->getRepository(Order::class)->findall();

        return $this->respond($orders);
    }

    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);

        $form->submit($data);

        if ($form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->respond($order);
        }

        return $this->getErrorsFromForm($form);
    }


    public function show($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        return $this->respond($order);
    }
}
