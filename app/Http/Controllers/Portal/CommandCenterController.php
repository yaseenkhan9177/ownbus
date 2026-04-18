<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\CommandCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandCenterController extends Controller
{
    protected $commandCenterService;

    public function __construct(CommandCenterService $commandCenterService)
    {
        $this->commandCenterService = $commandCenterService;
    }

    public function index()
    {
        $company = Auth::user()->company;
        $data = $this->commandCenterService->getSnapshot($company);

        return view('company.command-center', compact('data', 'company'));
    }

    public function apiSnapshot()
    {
        $company = Auth::user()->company;
        $data = $this->commandCenterService->getSnapshot($company);

        return response()->json($data);
    }
}
