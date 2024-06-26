<?php

namespace App\Http\Controllers;

use App\Services\RecordsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class RecordsController extends Controller
{
    public function __construct(protected RecordsService $recordsService)
    {
    }

    public function uploadRecords(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'mimes:csv'],
            ]);

            $responseData = $this->recordsService->processRecords($request->file('file'));

            return $this->success('Records processed successfully!', '', $responseData);
        } catch (Throwable $throwable) {
            dd($throwable);
            return $this->error('Error processing records', $throwable->getMessage());
        }
    }
}
