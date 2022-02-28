<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
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
            'poids' => $this->poids,
            'taille' => $this->taille,
            'dorsale_number' => $this->dorsale_number,
            'descriptions' => $this->descriptions,
            'position' => $this->position->libelle,
            'images' => $this->images,
        ];
    }
}
