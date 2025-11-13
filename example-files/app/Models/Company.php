<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'legal_name',
        'nit',
        'logo',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Get the users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the headquarters for the company.
     */
    public function headquarters(): HasMany
    {
        return $this->hasMany(Headquarters::class);
    }

    /**
     * Get the front securities for the company.
     */
    public function frontSecurities(): HasMany
    {
        return $this->hasMany(FrontSecurity::class);
    }

    /**
     * Get the modules enabled for the company.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(CompanyModule::class);
    }

    /**
     * Get all of the company's addresses.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to load company with its modules.
     */
    public function scopeWithModules($query)
    {
        return $query->with('modules');
    }

    /**
     * Check if a specific module is enabled for this company.
     */
    public function hasModuleEnabled(string $moduleKey): bool
    {
        return $this->modules()
            ->where('module_key', $moduleKey)
            ->where('enabled', true)
            ->exists();
    }

    /**
     * Get the primary address for the company.
     */
    public function primaryAddress()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }
}
