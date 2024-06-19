<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationStoreRequest;
use App\Interfaces\NotificationInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationInterface $notificationInterface;

    public function __construct(NotificationInterface $notificationInterface)
    {
        $this->notificationInterface = $notificationInterface;
    }

    public function index(Request $request)
    {
        return $this->notificationInterface->index($request);
    }

    public function create()
    {
        return $this->notificationInterface->create();
    }

    public function store(NotificationStoreRequest $request)
    {
        return $this->notificationInterface->store($request);
    }

    public function delete(Request $request)
    {
        return $this->notificationInterface->delete($request);
    }
}
