<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'titre' => $this->titre,
            'descriptions' => $this->descriptions,
            'date_post' => $this->date_post,
            'posted_by' => $this->user->name,
            'images' => $this->images,
        ];
    }
}
