<?php

namespace Modules\User\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\User\Entities\Company;
use Modules\User\Entities\CompanyPrice;

class CompanyController
{

    public function index()
    {
        $company = Company::query()
                          ->paginate(12);
        return view('user::admin.company.index', compact('company'));
    }

    public function store(Request $request)
    {

        Company::query()
               ->create([
                            'title' => $request->get('title'),
                            'company_price_id' => $request->get('companyPrice'),
                        ]);

        return redirect()
            ->back()
            ->with('success', 'Başarıyla Eklendi');
    }

    public function create()
    {
        $companyPrices = CompanyPrice::query()
                                     ->get()
                                     ->pluck('title', 'id');
        return view('user::admin.company.create', compact('companyPrices'));
    }

    public function edit(int $id)
    {
        $company = Company::query()
                          ->find($id);

        $companyPrices = CompanyPrice::query()
                                     ->get();
        return view('user::admin.company.edit', compact('company', 'companyPrices'));
    }

    public function update(Request $request, $id){

        $company = Company::query()
                          ->find($id);
        $company->update([
                             'title' => $request->get('title'),
                             'company_price_id' => $request->get('companyPrice'),
                         ]);
        return redirect()
            ->back()
            ->with('success', 'Başarıyla Güncellendi');
    }

}
