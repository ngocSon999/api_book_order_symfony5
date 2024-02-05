<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class AbstractBaseController extends AbstractController
{
    public function getErrorsFromForm(FormInterface $form, $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $errors = $this->flattenFormErrors($form);

        return $this->json([
            'code' => 422,
            'message' => 'Validation error',
            'errors' => $errors,
        ], $code);
    }

    private function flattenFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            $childErrors = $this->flattenFormErrors($childForm);
            if (!empty($childErrors)) {
                foreach ($childErrors as $childError) {
                    $errors[$childForm->getName()] = $childError;
                }
            }
        }

        return $errors;
    }

    public function respond($data, $code = Response::HTTP_OK): JsonResponse
    {
        return $this->json([
            'code' => $code,
            'data' => $data
        ], $code);
    }

    public function respondError($data, $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json([
            'code' => $code,
            'data' => $data
        ], $code);
    }
}
