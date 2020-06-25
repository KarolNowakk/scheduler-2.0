<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
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
            'short_name' => $this->short_name,
            'job_title' => $this->job_title,
            'work_place' => $this->work_place,
            'salary' => $this->salary,
            'created_by' => $this->createdBy,
            'belongs_to' => $this->belongsTo,
        ];
    }
}
