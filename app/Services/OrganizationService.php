<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Dtos\OrganizationCreationDto;
use App\Exceptions\OrganizationException;
use App\Enums\Organization\OrganizationRole;
use App\Enums\Organization\OrganizationStatus;

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
        $organization = Organization::query()
            ->where('created_by', $user->id)
            ->where('status', '!=', OrganizationStatus::REJECTED)
            ->first();

        if($organization) {
            throw OrganizationException::userAlreadyHasOrganization($organization->name);
        }


        try {
            DB::beginTransaction();

            $organization = Organization::query()
                ->where('created_by', $user->id)
                ->where('name', $data->name)
                ->where('tax_number', $data->taxNumber)
                ->where('commercial_register_number', $data->commercialRegisterNumber)
                ->first();

            if ($organization) {
                $organization->update([
                    'status' => OrganizationStatus::PENDING,
                ]);
            } else {

                $organization = Organization::create([
                    'name' => $data->name,
                    'tax_number' => $data->taxNumber,
                    'commercial_register_number' => $data->commercialRegisterNumber,
                    'created_by' => $user->id,
                    'status' => OrganizationStatus::PENDING,
                ]);

                $organization->users()->attach($user->id, [
                    'role' => OrganizationRole::OWNER,
                    'joined_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $organization;
    }
}
