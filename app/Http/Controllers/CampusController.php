<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }
}
