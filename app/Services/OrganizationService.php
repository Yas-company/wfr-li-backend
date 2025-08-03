<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Dtos\OrganizationCreationDto;
use App\Enums\Organization\OrganizationRole;

class OrganizationService
{
    /**
     * Create a new organization.
     *
     * @param OrganizationCreationDto $data
     * @param User $user
     *
     * @return Organization
     */
    public function createOrganization(OrganizationCreationDto $data, User $user): Organization
    {
        try {
            DB::beginTransaction();

            $organization = Organization::create([
                'name' => $data->name,
                'tax_number' => $data->taxNumber,
                'commercial_register_number' => $data->commercialRegisterNumber,
                'created_by' => $user->id,
            ]);

            $organization->users()->attach($user->id, [
                'role' => OrganizationRole::OWNER,
                'joined_at' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $organization;
    }
}
