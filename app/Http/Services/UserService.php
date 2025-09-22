<?php

namespace App\Http\Services;

use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Suppliers\BuyerRepository;

class UserService
{
    public function __construct(private BuyerRepository $buyerRepository)
    {
        //
    }

    public function getSupplierFields(User $user)
    {
        return $user->fields;
    }

    // public function suppliers()
    // {
    // $suppliers = User::where('role', UserRole::SUPPLIER)
    //         ->where('status', UserStatus::APPROVED)
    //         ->with('fields')
    //         ->paginate(10);
    //     return $suppliers;
    // }
    public function suppliers($request)
    {
        $buyer = Auth::user();

        if (! $buyer) {
            return ['error' => 'User not authenticated'];
        }

        $query = User::with('fields', 'supplier')->where('role', UserRole::SUPPLIER)
            ->where('status', UserStatus::APPROVED);

        // Check if distance filtering is needed
        $hasDistanceFilter = $request->has('distance');
        $search = $request->input('search');

        if ($hasDistanceFilter) {
            $distance = $request->input('distance');

            // Validate distance input
            if (! is_numeric($distance) || $distance <= 0) {
                return ['error' => 'Invalid distance value'];
            }

            $buyerAddress = $buyer->defaultAddress;
            if (! $buyerAddress || ! $buyerAddress->latitude || ! $buyerAddress->longitude) {
                return ['error' => 'Buyer location not available'];
            }

            $latitude = $buyerAddress->latitude;
            $longitude = $buyerAddress->longitude;

            $query = $query->join('addresses', function ($join) {
                $join->on('users.id', '=', 'addresses.user_id')
                    ->where('addresses.is_default', '=', true);
            })
                ->selectRaw('
                users.*,
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(addresses.latitude)) *
                    cos(radians(addresses.longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(addresses.latitude))
                )) AS distance
            ', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $distance)
                ->orderBy('distance');

            // Handle search functionality after join (specify table name to avoid ambiguity)
            if ($search) {
                $query = $query->where('users.name', 'like', '%'.$search.'%');
            }
        } else {
            if ($search) {
                $query = $query->where('name', 'like', '%'.$search.'%');
            }
        }

        $suppliers = $query->with('fields', 'supplier')->paginate(10);

        // Process field names for display
        foreach ($suppliers as $supplier) {
            if ($supplier->fields) {
                foreach ($supplier->fields as $field) {
                    $decodedName = json_decode($field->name, true);
                    $field->name = (is_array($decodedName) && isset($decodedName['en']))
                        ? $decodedName['en']
                        : $field->name;
                }
            }
        }

        return $suppliers;
    }

    public function show(int $user_id)
    {
        $user = User::find($user_id);
        if (! $user || $user->role !== UserRole::SUPPLIER || $user->status !== UserStatus::APPROVED) {
            return ['error' => 'Supplier not found'];
        }
        $user->load(['fields']);

        return $user;
    }

    public function searchSuppliers($request)
    {
        $search = $request->search;
        $suppliers = User::where('name', 'like', '%'.$search.'%')
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
        if (! $distance) {
            return ['error' => 'Distance is required'];
        } else {
            // Get buyer's default address
            $buyerAddress = $buyer->defaultAddress;
            if (! $buyerAddress || ! $buyerAddress->latitude || ! $buyerAddress->longitude) {
                return ['error' => 'Buyer location not available'];
            }

            $latitude = $buyerAddress->latitude;
            $longitude = $buyerAddress->longitude;

            $users = DB::table('users')
                ->join('addresses', function ($join) {
                    $join->on('users.id', '=', 'addresses.user_id')
                        ->where('addresses.is_default', '=', true);
                })
                ->where('users.role', UserRole::SUPPLIER)
                ->select('users.*')
                ->selectRaw('
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(addresses.latitude)) *
                        cos(radians(addresses.longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(addresses.latitude))
                    )) AS distance
                ', [$latitude, $longitude, $latitude])
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
