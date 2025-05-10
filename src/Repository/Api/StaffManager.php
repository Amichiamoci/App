<?php

namespace App\Repository\Api;
use App\Entity\UserClaimLoad;
use App\Entity\Staff;

trait StaffManager
{
    
    /**
     * @return Staff[]
     */
    public function Staff(): array
    {
        return $this->_getObjectCollection(
            collectionName: 'staff-list', 
            className: Staff::class,
        );
    }

    public function CheckClaims(string $email): UserClaimLoad
    {
        /**
         * @var UserClaimLoad[]
         */
        $arr = $this->_getObjectCollection(
            collectionName: 'get-user-claims', 
            className: UserClaimLoad::class, 
            params: [
                'Email' => $email,
            ],
            cache: false,
        );
        if (count(value: $arr) === 0)
        {
            return new UserClaimLoad();
        }
        return $arr[0];
    }
}