<?php

namespace App\Services;

use App\Dtos\OrganizationCreationDto;
use App\Enums\Organization\OrganizationRole;
use App\Enums\Organization\OrganizationStatus;
use App\Exceptions\OrganizationException;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    /**
     * Create a new organization.
     */
    public function createOrganization(OrganizationCreationDto $data, User $user): Organization
    {
        $organization = Organization::query()
            ->where('created_by', $user->id)
            ->where('status', '!=', OrganizationStatus::REJECTED)
            ->first();

        if ($organization) {
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

    public function checkOrganization(User $user): ?Organization
    {
        $organization = $user->myOrganization;
        if (! $organization) {
            throw OrganizationException::userDoesNotHaveOrganization();
        }

        return $organization->load(['owner', 'users']);
    }

    public function updateOrganization(Organization $organization, array $data, User $user): Organization
    {
        if ($organization->created_by !== $user->id) {
            throw OrganizationException::userIsNotOwnerOfOrganization();
        }

        $organization->update($data);

        $organization->status = OrganizationStatus::PENDING;
        $organization->save();

        return $organization->load(['owner', 'users']);
    }
}
