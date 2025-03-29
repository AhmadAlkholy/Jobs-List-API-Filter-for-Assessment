<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Yajra\DataTables\EloquentDataTable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success(string $message = "success", $data = [], array $additionalData = [])
    {
        $response = [
            'message' => $message,
            'data' => $data,
            'errors' => []
        ];
        if ($additionalData) {
            $response = array_merge($response, $additionalData);
        }
        return response()->json($response, 200);
    }

    protected function failed(string $message = 'Sorry something went wrong', $errors = [], $status = 500)
    {
        return response()->json([
            'message' => $message,
            'data' => [],
            'errors' => $errors
        ], $status);
    }

    protected function paginate($message = 'Success', $paginator)
    {
        return response()->json([
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage()
            ],
            'errors' => []
        ]);
    }
}
