<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

return [
    'accepted'       => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Polje :attribute nije validan URL.',
    'after'          => 'Polje :attribute mora biti datum poslije :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => 'Polje :attribute može sadržati samo slova.',
    'alpha_dash'     => 'Polje :attribute može sadržati samo slova, brojeve i povlake.',
    'alpha_num'      => 'Polje :attribute može sadržati samo slova i brojeve.',
    'array'          => 'Polje :attribute mora biti niz.',
    'attributes'     => [
    ],
    'before'          => 'Polje :attribute mora biti datum prije :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => 'Polje :attribute mora biti između :min - :max karaktera.',
        'file'    => 'Fajl :attribute mora biti izmedju :min - :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti izmedju :min - :max.',
        'string'  => 'Polje :attribute mora biti izmedju :min - :max karaktera.'
    ],
    'boolean'          => 'Polje :attribute mora biti tačno ili netačno',
    'confirmed'        => 'Potvrda polja :attribute se ne poklapa.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Polje :attribute nema ispravan datum.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Polje :attribute nema odgovarajući format :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Polja :attribute i :other moraju biti različita.',
    'digits'         => 'Polje :attribute mora da sadži :digits brojeve.',
    'digits_between' => 'Polje :attribute mora biti između :min i :max broja.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'Format polja :attribute mora biti validan email.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Odabrano polje :attribute nije validno.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Polje :attribute je obavezno.',
    'gt'             => [
        'array'   => 'The :attribute must have more than :value items.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string'  => 'The :attribute must be greater than :value characters.'
    ],
    'gte' => [
        'array'   => 'The :attribute must have :value items or more.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'string'  => 'The :attribute must be greater than or equal :value characters.'
    ],
    'image'    => 'Polje :attribute mora biti slika.',
    'in'       => 'Odabrano polje :attribute nije validno.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => 'Polje :attribute mora biti broj.',
    'ip'       => 'Polje :attribute mora biti validna IP adresa.',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => 'The :attribute must be a valid JSON string.',
    'lt'       => [
        'array'   => 'The :attribute must have less than :value items.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string'  => 'The :attribute must be less than :value characters.'
    ],
    'lte' => [
        'array'   => 'The :attribute must not have more than :value items.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal :value.',
        'string'  => 'The :attribute must be less than or equal :value characters.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Polje :attribute mora sadržati manje od :max karaktera.',
        'file'    => 'Polje :attribute mora biti manje od :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :max.',
        'string'  => 'Polje :attribute mora sadržati manje od :max karaktera.'
    ],
    'mimes'     => 'Polje :attribute mora biti fajl tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti fajl tipa: :values.',
    'min'       => [
        'array'   => 'Polje :attribute mora sadržati najmanje :min karaktera.',
        'file'    => 'Fajl :attribute mora biti najmanje :min kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :min.',
        'string'  => 'Polje :attribute mora sadržati najmanje :min karaktera.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Odabrani element polja :attribute nije validan.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'Polje :attribute mora biti broj.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'The :attribute field must be present.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Polje :attribute ima neispravan format.',
    'required'             => 'Polje :attribute je obavezno.',
    'required_if'          => 'Polje :attribute je obavezno kada :other je :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'Polje :attribute je obavezno kada je :values prikazano.',
    'required_with_all'    => 'Polje :attribute je obavezno kada je :values prikazano.',
    'required_without'     => 'Polje :attribute je obavezno kada :values nije prikazano.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno :values nije prikazano.',
    'same'                 => 'Polja :attribute i :other se moraju poklapati.',
    'size'                 => [
        'array'   => 'Polje :attribute mora biti :size karaktera.',
        'file'    => 'Fajl :attribute mora biti :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string'  => 'Polje :attribute mora biti :size karaktera.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'Polje :attribute mora sadrzavati slova.',
    'timezone'    => 'Polje :attribute mora biti ispravna vremenska zona.',
    'unique'      => 'Polje :attribute već postoji.',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => 'Format polja :attribute nije validan.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
