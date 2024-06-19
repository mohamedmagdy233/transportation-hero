<?php

namespace App\Interfaces;

use App\Http\Requests\NotificationStoreRequest;

Interface NotificationInterface {

    public function index($request);

    public function delete($request);

    public function create();

    public function store(NotificationStoreRequest $request);


}
