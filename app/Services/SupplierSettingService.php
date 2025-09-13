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

    public function updateMinOrderAmount(array $data, User $user)
    {
        $setting = Setting::updateOrCreate(['user_id' => $user->id, 'key' => OrderSettings::ORDER_MIN_ORDER_AMOUNT->value],
            ['value' => $data['min_order_amount']]
        );

        return $setting;
    }

    public function getSupplierSettings(User $user)
    {
        return Setting::where('user_id', $user->id)->get();
    }
}
