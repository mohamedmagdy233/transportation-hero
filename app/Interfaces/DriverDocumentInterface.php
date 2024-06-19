<?php

namespace App\Interfaces;

Interface DriverDocumentInterface {

    public function index($request);

    public function edit($driver_document);

    public function changeStatusDocument($request);
    
}
