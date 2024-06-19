<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\DriverDocumentInterface;
use App\Models\DriverDocuments;
use Illuminate\Http\Request;

class DriverDocumentController extends Controller
{
    protected DriverDocumentInterface $driverDocumentInterface;

    public function __construct(DriverDocumentInterface $driverDocumentInterface)
    {
        $this->driverDocumentInterface = $driverDocumentInterface;
    }

    public function index(Request $request)
    {
        return $this->driverDocumentInterface->index($request);
    }

    public function edit(DriverDocuments $driver_document)
    {
        return $this->driverDocumentInterface->edit($driver_document);
    }

    public function changeStatusDocument(Request $request)
    {
        return $this->driverDocumentInterface->changeStatusDocument($request);
    }
}
