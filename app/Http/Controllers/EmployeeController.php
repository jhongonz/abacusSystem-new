<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    private DataTables $dataTable;
    public function __construct(
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->dataTable = $dataTable;
    }

    public function index(): JsonResponse|string
    {
        $view = view('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function getEmployees(Request $request): JsonResponse
    {

    }
}
