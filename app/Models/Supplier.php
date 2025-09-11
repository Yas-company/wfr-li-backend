<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Supplier extends Model
{
    use Searchable;

    protected $fillable = ['user_id', 'is_open'];

    public function searchableAs()
    {
        return 'suppliers_geo_index';
    }

    public function shouldBeSearchable(): bool
    {
        return $this->user->addresses()->exists();
    }

    public function toSearchableArray()
    {
        $this->load([
            'user',
            'user.fields',
            'user.addresses'
        ]);

        $geoloc = [];

        foreach ($this->user->addresses as $address) {
            if ($address['latitude'] && $address['longitude']) {
                $geoloc[] = [
                    'lat' => (float) $address['latitude'],
                    'lng' => (float) $address['longitude'],
                ];
            }
        }

        $array = $this->toArray();

        return [
            'id' => $array['user']['id'],
            'name' => $array['user']['name'],
            'phone' => $array['user']['phone'],
            'business_name' => $array['user']['business_name'],
            'fields' => array_map(fn($field) => $field['name'], $array['user']['fields']),
            '_geoloc' => $geoloc,
            'image' => asset('storage/'.$array['user']['image']),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
