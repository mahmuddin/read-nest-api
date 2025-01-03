<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AuthorResource extends JsonResource
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
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'bio' => $this->bio,
            'profile_image' => Storage::url($this->profile_image),
            'social_media' => $this->social_media,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date,
            'categories' => $this->categories,
        ];
    }
}
