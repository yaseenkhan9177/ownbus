<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SaaS\SystemMonitorService;

class SystemController extends Controller
{
    protected $monitorService;

    public function __construct(SystemMonitorService $monitorService)
    {
        $this->monitorService = $monitorService;
    }

    public function index()
    {
        $systemInfo = $this->monitorService->getDiagnostics();
        return view('admin.system.index', compact('systemInfo'));
    }
}
