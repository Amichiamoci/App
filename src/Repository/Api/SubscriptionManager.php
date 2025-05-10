<?php

namespace App\Repository\Api;
use App\Entity\IdentityDocumentType;
use App\Entity\Subscription;
use App\Entity\Anagraphical;

trait SubscriptionManager
{
    
    /**
     * @param string $email
     * @return Anagraphical[]
     */
    public function ManagedAnagraphicals(string $email): array
    {
        return $this->_getObjectCollection(
            collectionName: 'managed-anagraphicals', 
            className: Anagraphical::class,
            params: [
                'Email' => $email
            ],
            cache: 1800, // Every method that changes anagraphical data must invalidate the cache
        );
    }

    private function InvalidateAnagraphicals(string $email): bool
    {
        return $this->_cacheInvalidate(
            collectionName: 'managed-anagraphicals', 
            params: [
                'Email' => $email,
            ],
            duration: 1800,
        );
    }

    /**
     * @return IdentityDocumentType[]
     */
    public function DocumentTypes(): array
    {
        return $this->_getObjectCollection(
            collectionName: 'document-types', 
            className: IdentityDocumentType::class,
        );
    }

    /**
     * Returns true if the current email is associted to a subscribed person
     * or to a parent of a subscribed person
     * @param string $email The email to check for, case INSENSITIVE
     * @return bool
     */
    public function IsSubscribedOrParentOfSubscribed(string $email): bool
    {
        return array_any(
            array: $this->ManagedAnagraphicals($email), 
            callback: function (Anagraphical $a): bool {
                return $a->hasSubscription();
            },
        );
    }

    /**
     * Returns true if the current email is associted to a subscribed person
     * @param string $email The email to check for, case INSENSITIVE
     * @return bool
     */
    public function IsSubscribed(string $email): bool
    {
        return array_any(
            array: $this->ManagedAnagraphicals($email), 
            callback: function (Anagraphical $a) use($email): bool {
                return $a->hasSubscription() && strtolower(string: $a->Email) === strtolower(string: $email);
            },
        );
    }

    public function HandleAnagraphical(
        Anagraphical $anagraphical,
        string $userId,
    ): ?Anagraphical
    {
        $parameters = [
            'Name' => $anagraphical->Name,
            'Surname' => $anagraphical->Surname,
            'Taxcode' => $anagraphical->TaxCode,
            'Email' => $anagraphical->Email,
            'Birthdate' => $anagraphical->BirthDate,

            'Documentexpiration' => $anagraphical->Document->Expiration->format(format: 'Y-m-d'),
            'Documentcode' => $anagraphical->Document->Code,
            'Documenttype' => $anagraphical->Document->Type->Id,
        ];

        if (!empty($anagraphical->Id))
        {
            $parameters['Id'] = $anagraphical->Id;
        }
        if ($anagraphical->hasPhone())
        {
            $parameters['Phone'] = $anagraphical->Phone;
        }
        if ($anagraphical->hasBirtPlace())
        {
            $parameters['Birthplace'] = $anagraphical->BirthPlace;
        }

        $result = $this->_getObjectCollection(
            collectionName: 'anagraphical', 
            className: Anagraphical::class, 
            params: $parameters,
            cache: false,
        );
        $this->InvalidateAnagraphicals($userId);


        if (count(value: $result) === 0)
        {
            if (empty($subscription->Id))
            {
                // Could not add the record
                return null;
            }
            return $anagraphical; // Successfully edited record
        }
        return $result[0];
    }
    
    public function HandleSubscription(
        int $anagraphical, 
        string $userId,
        Subscription $subscription,
    ): ?Subscription
    {
        $parameters = [
            'Anagraphical' => $anagraphical,
            'Church' => $subscription->getChurch()->getId(),
            'Shirt' => $subscription->getShirt(),
        ];
        if (!empty($subscription->Id))
        {
            // Is editing an existing subscription
            $parameters['Id'] = $subscription->Id;
        }

        $result = $this->_getObjectCollection(
            collectionName: 'subscribe', 
            className: Subscription::class, 
            params: $parameters,
            cache: false,
        );
        $this->InvalidateAnagraphicals($userId);

        if (count(value: $result) === 0)
        {
            if (empty($subscription->Id))
            {
                // Could not add the record
                return null;
            }
            return $subscription; // Successfully edited record
        }
        return $result[0];
    }

    public function SubscriptionCertificate(
        int $subscriptionId, 
        string $filePath
    ): bool
    {
        return false;
    }

}