<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessBoletoRequest;
use App\Services\BoletoService;
use Illuminate\Http\JsonResponse;
use Throwable;

class BoletoController extends Controller
{

    /**
     * @param ProcessBoletoRequest $request
     * @return JsonResponse
     */
    public function handleFile(ProcessBoletoRequest $request): JsonResponse
    {
        try {
            $boletoService = new BoletoService($request->validated('file'));
            $boletoService->handle();

            return $this->success('Boletos processados com sucesso');
        } catch (Throwable $throwable) {
            return $this->error(
                'Não foi possivel seguir com a requisição.',
                $throwable->getMessage(),
                $throwable->getCode()
            );
        }
    }
}
