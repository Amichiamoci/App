<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use CodiceFiscale\InverseCalculator;
use nicholasricci\AnagraficheANPRISTAT\Collection\ComuneCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Order;

class Anagraphical
{
    #[Assert\NotEqualTo(value: 0)]
    public int $Id;
    public function getId(): int { return $this->Id; }

    #[Assert\NotBlank]
    public string $Name;

    #[Assert\NotBlank]
    public string $Surname;
    public function getName(): string { return $this->Name; }
    public function getSurname(): string { return $this->Surname; }

    public const TaxCodePattern = '[A-Za-z]{6}[0-9]{2}[ABCDEHLMPRSTabcdehlmprst]{1}[0-9]{2}[A-Za-z]{1}[0-9LMNPQRSTUVlmnpqrstuv]{3}[A-Za-z]{1}';
    #[Assert\Regex(pattern: '/' . self::TaxCodePattern . '/')]
    #[Assert\NotBlank]
    public string $TaxCode;
    public function getTaxCode(): string { return $this->TaxCode; }
    
    #[Assert\Email]
    public ?string $Email;
    public ?string $Phone;
    public function getEmail(): ?string { return $this->Email; }
    public function getPhone(): ?string { return $this->Phone; }
    public function hasEmail(): bool { return is_string(value: $this->Email) && strlen(string: trim(string: $this->Email)) > 0; }
    public function hasPhone(): bool { return is_string(value: $this->Phone) && strlen(string: trim(string: $this->Phone)) > 0; }

    public ?string $BirthDate = null;
    public function hasBirtDate(): bool { return is_string(value: $this->BirthDate) && strlen(string: trim(string: $this->BirthDate)) > 0; }
    public function getBirthDate(): ?string { return $this->BirthDate; }

    public ?string $BirthPlace = null;
    public function hasBirtPlace(): bool { return !empty($this->BirthPlace); }
    public function getBirtPlace(): ?string { return $this->BirthPlace; }

    public IdentityDocument $Document;
    public function getDocument(): IdentityDocument { return $this->Document; }

    public ?Subscription $Subscription = null;
    public function hasSubscription(): bool { return isset($this->Subscription); }
    public function getSubscription(): ?Subscription { return $this->Subscription; }

    public array $Status = [];
    public function hasStatus(): bool { return count(value: $this->Status) > 0; }
    public function getStatus(): array { return $this->Status; }

    public function reverseTaxCode(): bool
    {
        if (strlen(string: $this->TaxCode) === 0)
        {
            return false;
        }

        try {
            $cf = new InverseCalculator(codiceFiscale: $this->TaxCode)->getSubject();

            $this->BirthDate = $cf->getBirthDate()->format(format: 'Y-m-d');
            $belfiore = $cf->getBelfioreCode();

            $criteria = new Criteria();
            $criteria
                ->where(expression: new Comparison(
                    field: 'registry_code', 
                    op: Comparison::IS, 
                    value: $belfiore,
                ))
                ->andWhere(expression: new Comparison(
                    field: 'status', 
                    op: Comparison::IS, 
                    value: 'active',
                ))
                ->orderBy(orderings: [
                    'last_update' => Order::Ascending,
                ])
            ;

            $places = (new ComuneCollection())->matching(criteria: $criteria);
            if ($places->isEmpty())
            {
                return false;
            }
            $place = $places->first();
            $this->BirthPlace = $place['name_it'] . ', ' . $place['provincial_code'];
            
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}