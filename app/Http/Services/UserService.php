<?php

namespace App\Http\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Models\UserField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
public function getSupplierFields()
    {
        $user = Auth::user();
        $fields = UserField::where('user_id', $user->id)->with('field')->get();
        return $fields;
    }

    public function suppliers()
    {
    $suppliers = User::where('role', UserRole::SUPPLIER)
            ->where('status', UserStatus::APPROVED)
            ->with('fields')
            ->paginate(10);
        return $suppliers;
    }

    public function show(int $user_id)
    {
        $user = User::find($user_id);
        if (!$user || $user->role !== UserRole::SUPPLIER || $user->status !== UserStatus::APPROVED) {
            return ['error' => 'Supplier not found'];
        }
    $user->load('categories')->with('fields');
        return $user;
    }
    public function searchSuppliers($request)
    {
        $search = $request->search;
        $suppliers = User::where('name', 'like', '%' . $search . '%')
            ->where('role', UserRole::SUPPLIER)
            ->where('status', UserStatus::APPROVED)
            ->with('fields')
            ->paginate(10);
        return $suppliers;
    }
    public function filter($request)
    {
        $buyer = Auth::user(); 
        $distance = $request->input('distance'); 
        if(!$distance){
            return ['error' => 'Distance is required'];
        } else {
            $latitude = $buyer->latitude;
            $longitude = $buyer->longitude;

         
            $users = DB::table('users')
                ->where('role', UserRole::SUPPLIER)
                ->select('*')
                ->selectRaw("
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(latitude))
                    )) AS distance
                ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $distance)
                ->orderBy('distance')
                ->paginate(10);

            
            $userIds = $users->pluck('id')->toArray();

            $fields = DB::table('user_fields')
                ->join('fields', 'user_fields.field_id', '=', 'fields.id')
                ->whereIn('user_fields.user_id', $userIds)
                ->select('user_fields.user_id', 'fields.*')
                ->get()
                ->groupBy('user_id');

        
            foreach ($users as $user) {
                $user->fields = $fields->get($user->id, collect())->values();
                foreach ($user->fields as $field) {
                    $decodedName = json_decode($field->name, true);
                    $field->name = (is_array($decodedName) && isset($decodedName['en']))
                        ? $decodedName['en']
                        : $field->name;
                }
            }
        }
        return $users;
    }   
}
