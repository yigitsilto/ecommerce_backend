<?php

namespace Modules\Import\Http\Controllers\Admin;

class DownloadCsvController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function index()
    {
        $import_types = ['product' => 'products.csv'];

        if (array_key_exists(request('import_type'), $import_types)) {
            $path = public_path('csv_templates/' . $import_types[request('import_type')]);

            return response()->download($path);
        }
    }
}
