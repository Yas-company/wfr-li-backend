<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Success Response
     *
     * @param mixed|null $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('messages.success'),
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error Response
     *
     * @param string|null $message
     * @param mixed|null $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse(
        ?string $message = null,
        mixed $errors = null,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message ?? __('messages.error'),
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Created Response
     *
     * @param mixed|null $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse(
        mixed $data = null,
        ?string $message = null
    ): JsonResponse {
        return $this->successResponse($data, $message ?? __('messages.created'), Response::HTTP_CREATED);
    }

    /**
     * No Content Response
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Unauthorized Response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?? __('messages.unauthorized'), null, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Forbidden Response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?? __('messages.forbidden'), null, Response::HTTP_FORBIDDEN);
    }

    /**
     * Not Found Response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function notFoundResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?? __('messages.not_found'), null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Validation Error Response
     *
     * @param mixed $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(
        mixed $errors,
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?? __('messages.validation_failed'), $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Server Error Response
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function serverErrorResponse(
        ?string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?? __('messages.server_error'), null, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function paginatedResponse(AbstractPaginator $paginator, $resourceCollection, ?string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('messages.success'),
            'data' => [
                'products' => $resourceCollection,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ],
        ], $statusCode);
    }
}
