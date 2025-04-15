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
    'active_url'     => 'Polje :attribute nije ispravan URL.',
    'after'          => 'Polje :attribute mora biti datum nakon :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum veći ili jednak :date.',
    'alpha'          => 'Polje :attribute smije sadržavati samo slova.',
    'alpha_dash'     => 'Polje :attribute smije sadržavati samo slova, brojeve i crtice.',
    'alpha_num'      => 'Polje :attribute smije sadržavati samo slova i brojeve.',
    'array'          => 'Polje :attribute mora biti niz.',
    'attributes'     => [
    ],
    'before'          => 'Polje :attribute mora biti datum prije :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum manji ili jednak :date.',
    'between'         => [
        'array'   => 'Polje :attribute mora imati između :min - :max stavki.',
        'file'    => 'Polje :attribute mora biti između :min - :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti između :min - :max.',
        'string'  => 'Polje :attribute mora biti između :min - :max znakova.'
    ],
    'boolean'          => 'Polje :attribute mora biti false ili true.',
    'confirmed'        => 'Potvrda polja :attribute se ne podudara.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Polje :attribute nije ispravan datum.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Polje :attribute ne podudara s formatom :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Polja :attribute i :other moraju biti različita.',
    'digits'         => 'Polje :attribute mora sadržavati :digits znamenki.',
    'digits_between' => 'Polje :attribute mora imati između :min i :max znamenki.',
    'dimensions'     => 'Polje :attribute ima neispravne dimenzije slike.',
    'distinct'       => 'Polje :attribute ima dupliciranu vrijednost.',
    'email'          => 'Polje :attribute mora biti ispravna e-mail adresa.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Odabrano polje :attribute nije ispravno.',
    'file'           => 'Polje :attribute mora biti datoteka.',
    'filled'         => 'Polje :attribute je obavezno.',
    'gt'             => [
        'array'   => 'Polje :attribute mora biti veće od :value stavki.',
        'file'    => 'Polje :attribute mora biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od :value.',
        'string'  => 'Polje :attribute mora biti veće od :value karaktera.'
    ],
    'gte' => [
        'array'   => 'Polje :attribute mora imati :value stavki ili više.',
        'file'    => 'Polje :attribute mora biti veće ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće ili jednako :value.',
        'string'  => 'Polje :attribute mora biti veće ili jednako :value znakova.'
    ],
    'image'    => 'Polje :attribute mora biti slika.',
    'in'       => 'Odabrano polje :attribute nije ispravno.',
    'in_array' => 'Polje :attribute ne postoji u :other.',
    'integer'  => 'Polje :attribute mora biti broj.',
    'ip'       => 'Polje :attribute mora biti ispravna IP adresa.',
    'ipv4'     => 'Polje :attribute mora biti ispravna IPv4 adresa.',
    'ipv6'     => 'Polje :attribute mora biti ispravna IPv6 adresa.',
    'json'     => 'Polje :attribute mora biti ispravan JSON string.',
    'lt'       => [
        'array'   => 'Polje :attribute mora biti manje od :value stavki.',
        'file'    => 'Polje :attribute mora biti manje od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :value.',
        'string'  => 'Polje :attribute mora biti manje od :value znakova.'
    ],
    'lte' => [
        'array'   => 'Polje :attribute ne smije imati više od :value stavki.',
        'file'    => 'Polje :attribute mora biti manje ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje ili jednako :value.',
        'string'  => 'Polje :attribute mora biti manje ili jednako :value znakova.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Polje :attribute ne smije imati više od :max stavki.',
        'file'    => 'Polje :attribute mora biti manje od :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :max.',
        'string'  => 'Polje :attribute mora sadržavati manje od :max znakova.'
    ],
    'mimes'     => 'Polje :attribute mora biti datoteka tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'min'       => [
        'array'   => 'Polje :attribute mora sadržavati najmanje :min stavki.',
        'file'    => 'Polje :attribute mora biti najmanje :min kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :min.',
        'string'  => 'Polje :attribute mora sadržavati najmanje :min znakova.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Odabrano polje :attribute nije ispravno.',
    'not_regex'   => 'Format polja :attribute je neispravan.',
    'numeric'     => 'Polje :attribute mora biti broj.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Polje :attribute mora biti prisutno.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Polje :attribute se ne podudara s formatom.',
    'required'             => 'Polje :attribute je obavezno.',
    'required_if'          => 'Polje :attribute je obavezno kada polje :other sadrži :value.',
    'required_unless'      => 'Polje :attribute je obavezno osim :other je u :values.',
    'required_with'        => 'Polje :attribute je obavezno kada postoji polje :values.',
    'required_with_all'    => 'Polje :attribute je obavezno kada postje polja :values.',
    'required_without'     => 'Polje :attribute je obavezno kada ne postoji polje :values.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od polja :values ne postoji.',
    'same'                 => 'Polja :attribute i :other se moraju podudarati.',
    'size'                 => [
        'array'   => 'Polje :attribute mora sadržavati :size stavki.',
        'file'    => 'Polje :attribute mora biti :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string'  => 'Polje :attribute mora biti :size znakova.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'Polje :attribute mora biti string.',
    'timezone'    => 'Polje :attribute mora biti ispravna vremenska zona.',
    'unique'      => 'Polje :attribute već postoji.',
    'uploaded'    => 'Polje :attribute nije uspešno učitano.',
    'url'         => 'Polje :attribute nije ispravnog formata.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
