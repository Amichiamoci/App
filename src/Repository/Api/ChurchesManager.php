<?php

namespace App\Repository\Api;
use App\Entity\Staff;
use App\Entity\Church\Church;
use App\Entity\Church\ChurchScore;

trait ChurchesManager
{
    
    public function Church(int $id): ?Church
    {
        $churches = $this->_getObjectCollection(
            collectionName: 'church', 
            className: Church::class, 
            params: [
                'Id' => $id
            ]
        );
        if (count(value: $churches) === 0) {
            return null;
        }
        $church = $churches[0];
        $staff = $this->Staff();
        $church->Staff = array_filter(array: $staff, callback: function (Staff $s) use($church): bool {
            return $s->ChurchId === $church->Id;
        });
        return $church;
    }

    public function Churches(): array
    {
        return $this->_getObjectCollection(
            collectionName: 'churches', 
            className: Church::class,
        );
    }
    public function Leaderboard(): array
    {
        return $this->_getObjectCollection(collectionName: 'leaderboard', className: ChurchScore::class);
    }
}