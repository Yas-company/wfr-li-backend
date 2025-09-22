<?php

namespace App\Services;

use App\Enums\Settings\OrderSettings;
use App\Exceptions\UserException;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;

class SupplierSettingService
{
    public function updateSupplier(array $data, User $user)
    {

        if (! $user->supplier) {
            throw UserException::supplierNotExists();
        }
        $supplier = $user->supplier;
        $supplier->update($data);

        // Return the user with supplier and fields relationships loaded
        return $user->load(['supplier', 'fields']);
    }

    public function setSetting(array $data, User $user)
    {
        $setting = Setting::updateOrCreate(['user_id' => $user->id, 'key' => $data['key']],
            ['value' => $data['value']]
        );

        return $setting;
    }

    public function getSupplierSettings(User $user)
    {
        return Setting::where('user_id', $user->id)->get();
    }
}
