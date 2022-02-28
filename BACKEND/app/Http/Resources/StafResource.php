<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StafResource extends JsonResource
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
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'postnom' => $this->postnom,
            'date_naiss' => $this->date_naiss,
            'lieu_naiss' => $this->lieu_naiss,
            'date_deb_car' => $this->date_deb_car,
            'date_deb_equipe' => $this->date_deb_equipe,
            'nationality' => $this->nationality,
            'descriptions' => $this->descriptions,
            'position' => $this->attribution->libelle,
            'images' => $this->images,
        ];
    }
}
