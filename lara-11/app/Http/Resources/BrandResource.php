<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use File;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description ?: '',
            'website' => $this->website ?: '',
            'logo' => $this->getLogoUrl(),
            'status' => $this->status,
            'order' => $this->order,
            'company_id' => $this->company_id,
            'company_name' => $this->whenLoaded('company', $this->company->name) ?: 'Default',
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company' => $this->whenLoaded('company', $this->company),
        ];
    }

    protected function getLogoUrl()
    {
        $defaultLogo = asset('common/avater.png');

        if (!empty($this->logo) && File::exists(public_path('/uploads/brand/' . $this->logo))) {
            return asset('/uploads/brand/' . $this->logo);
        }

        return $defaultLogo;
    }
}