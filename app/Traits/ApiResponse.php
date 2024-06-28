<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function success(
        string $message,
        mixed $messageDev = '',
        array|JsonResource $data = [],
        int $code = Response::HTTP_OK
    ): JsonResponse {
        if ($data instanceof JsonResource) {
            $data = $data->jsonSerialize();
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => $message,
                'message-for-dev' => $messageDev,
                'data' => $data,
            ],
            $code
        );
    }

    /**
     * Return an error JSON response.
     */
    protected function error(
        string $message = 'Não foi possivel seguir com a requisição.',
        mixed $messageDev = '',
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        if ($code === 0 || $code > Response::HTTP_INTERNAL_SERVER_ERROR) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => $message,
                'message-for-dev' => $messageDev,
            ],
            $code
        );
    }
}
