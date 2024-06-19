<?php

namespace App\Interfaces;

use App\Models\City;

Interface AreaInterface {

    public function index($request);

    public function delete($request);

    public function create();

    public function store($request);

    public function edit($area);

    public function update($request, $id);

}