<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function success(
        string $message,
        string $messageDev = '',
        array|JsonResource $data = [],
        int $code = 200
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
        string $messageDev = '',
        int $code = 500
    ): JsonResponse {
        if ($code === 0 || $code > 500) {
            $code = 500;
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
