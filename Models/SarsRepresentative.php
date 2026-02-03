<?php

namespace Modules\SARSRepresentative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SarsRepresentative extends Model
{
    protected $table = 'sars_representatives';

    protected $fillable = [
        'full_name',
        'id_number',
        'passport_number',
        'email',
        'mobile',
        'capacity',
    ];

    /**
     * Capacity enum values.
     */
    const CAPACITY_DIRECTOR = 'director';
    const CAPACITY_SOLE_DIRECTOR = 'sole_director';
    const CAPACITY_TRUSTEE = 'trustee';
    const CAPACITY_SOLE_TRUSTEE = 'sole_trustee';
    const CAPACITY_ACCOUNTING_OFFICER = 'accounting_officer';
    const CAPACITY_CHAIRPERSON = 'chairperson';
    const CAPACITY_TREASURER = 'treasurer';
    const CAPACITY_ACCOUNTANT = 'accountant';
    const CAPACITY_AUTHORISED_REPRESENTATIVE = 'authorised_representative';

    const CAPACITIES = [
        self::CAPACITY_DIRECTOR,
        self::CAPACITY_SOLE_DIRECTOR,
        self::CAPACITY_TRUSTEE,
        self::CAPACITY_SOLE_TRUSTEE,
        self::CAPACITY_ACCOUNTING_OFFICER,
        self::CAPACITY_CHAIRPERSON,
        self::CAPACITY_TREASURER,
        self::CAPACITY_ACCOUNTANT,
        self::CAPACITY_AUTHORISED_REPRESENTATIVE,
    ];

    /**
     * Get the SARS representative requests for this representative.
     */
    public function sarsRepRequests(): HasMany
    {
        return $this->hasMany(SarsRepRequest::class, 'sars_representative_id');
    }

    /**
     * Get a human-readable label for a given capacity value.
     */
    public static function getCapacityLabel(string $capacity): string
    {
        $labels = [
            self::CAPACITY_DIRECTOR                 => 'Director',
            self::CAPACITY_SOLE_DIRECTOR             => 'Sole Director',
            self::CAPACITY_TRUSTEE                   => 'Trustee',
            self::CAPACITY_SOLE_TRUSTEE              => 'Sole Trustee',
            self::CAPACITY_ACCOUNTING_OFFICER        => 'Accounting Officer',
            self::CAPACITY_CHAIRPERSON               => 'Chairperson',
            self::CAPACITY_TREASURER                 => 'Treasurer',
            self::CAPACITY_ACCOUNTANT                => 'Accountant',
            self::CAPACITY_AUTHORISED_REPRESENTATIVE => 'Authorised Representative',
        ];

        return $labels[$capacity] ?? ucfirst(str_replace('_', ' ', $capacity));
    }
}
