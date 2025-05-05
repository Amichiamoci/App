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
            ]
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

    
    public function HandleSubscription(int $anagraphical, Subscription $subscription): ?Subscription
    {
        $result = $this->getObjectCollection(
            collectionName: 'subscribe', 
            className: Subscription::class, 
            params: [
                'Id' => $subscription->getId(),
                'Anagraphical' => $anagraphical,
                'Church' => $subscription->getChurch()->getId(),
                'Shirt' => $subscription->getShirt(),
            ],
        );
        if (count(value: $result) === 0)
        {
            if (empty($subscription->getId()))
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