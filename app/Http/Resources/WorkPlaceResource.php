<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkPlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->withoutWrapping();
        
        return [
            'name' => $this->name,
            'logo_path' => $this->logo_path,
            'address' => $this->address,
            'added_by' => $this->createdBy,
        ];
    }
}
