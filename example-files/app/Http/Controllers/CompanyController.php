<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies with filtering, sorting, and pagination.
     */
    public function index(Request $request): Response
    {
        $companies = QueryBuilder::for(Company::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        $query->where('name', 'LIKE', "%{$value}%")
                            ->orWhere('legal_name', 'LIKE', "%{$value}%")
                            ->orWhere('nit', 'LIKE', "%{$value}%");
                    });
                }),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('legal_name'),
                AllowedFilter::partial('nit'),
                AllowedFilter::exact('active'),
            ])
            ->allowedSorts(['name', 'legal_name', 'nit', 'active', 'created_at'])
            ->defaultSort('-created_at')
            ->with(['modules'])
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('companies/Index', [
            'companies' => CompanyResource::collection($companies),
            'filters' => $request->only(['filter', 'sort']),
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): Response
    {
        return Inertia::render('companies/Create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company = Company::create($data);

        return to_route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): Response
    {
        $company->load(['modules', 'headquarters', 'users', 'addresses']);

        return Inertia::render('companies/Show', [
            'company' => new CompanyResource($company),
        ]);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): Response
    {
        return Inertia::render('companies/Edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                \Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($data);

        return to_route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified company from storage (soft delete).
     */
    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return to_route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
