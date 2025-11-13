<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'nit' => $this->nit,
            'logo' => $this->logo ? asset('storage/'.$this->logo) : null,
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships (only when loaded)
            'modules' => $this->whenLoaded('modules'),
            'headquarters' => $this->whenLoaded('headquarters'),
            'users' => $this->whenLoaded('users'),
            'addresses' => $this->whenLoaded('addresses'),

            // Computed properties
            'modules_count' => $this->whenCounted('modules'),
            'headquarters_count' => $this->whenCounted('headquarters'),
            'users_count' => $this->whenCounted('users'),
        ];
    }
}
