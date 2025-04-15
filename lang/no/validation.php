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
    'accepted'       => ':attribute må aksepteres.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute er ikke en gyldig URL.',
    'after'          => ':attribute må være en dato etter :date.',
    'after_or_equal' => ':attribute må være en dato etter eller lik :date.',
    'alpha'          => ':attribute må kun bestå av bokstaver.',
    'alpha_dash'     => ':attribute må kun bestå av bokstaver, tall og bindestreker.',
    'alpha_num'      => ':attribute må kun bestå av bokstaver og tall.',
    'array'          => ':attribute må være en matrise.',
    'attributes'     => [
    ],
    'before'          => ':attribute må være en dato før :date.',
    'before_or_equal' => ':attribute må være en dato før eller lik :date.',
    'between'         => [
        'array'   => ':attribute må ha mellom :min - :max elementer.',
        'file'    => ':attribute må være mellom :min - :max kilobyte.',
        'numeric' => ':attribute må være mellom :min - :max.',
        'string'  => ':attribute må være mellom :min - :max tegn.'
    ],
    'boolean'          => ':attribute må være sann eller usann',
    'confirmed'        => ':attribute er ikke likt bekreftelsesfeltet.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute er ikke en gyldig dato.',
    'date_equals'    => ':attribute må være en dato lik :date.',
    'date_format'    => ':attribute samsvarer ikke med formatet :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute og :other må være forskellige.',
    'digits'         => ':attribute må ha :digits siffer.',
    'digits_between' => ':attribute må være mellom :min og :max siffer.',
    'dimensions'     => ':attribute har ugyldige bildedimensjoner.',
    'distinct'       => ':attribute har en duplisert verdi.',
    'email'          => ':attribute må være en gyldig e-postadresse.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Det valgte :attribute er ugyldig.',
    'file'           => ':attribute må være en fil.',
    'filled'         => ':attribute må fylles ut.',
    'gt'             => [
        'array'   => ':attribute må ha flere enn :value elementer.',
        'file'    => ':attribute må være større enn :value kilobyte.',
        'numeric' => ':attribute må være større enn :value.',
        'string'  => ':attribute må være større enn :value tegn.'
    ],
    'gte' => [
        'array'   => ':attribute må ha :value elementer eller flere.',
        'file'    => ':attribute må være større enn eller lik :value kilobyte.',
        'numeric' => ':attribute må være større enn eller lik :value.',
        'string'  => ':attribute må være større enn eller lik :value tegn.'
    ],
    'image'    => ':attribute må være et bilde.',
    'in'       => 'Det valgte :attribute er ugyldig.',
    'in_array' => 'Det valgte :attribute eksisterer ikke i :other.',
    'integer'  => ':attribute må være et heltall.',
    'ip'       => ':attribute må være en gyldig IP-adresse.',
    'ipv4'     => ':attribute må være en gyldig IPv4-adresse.',
    'ipv6'     => ':attribute må være en gyldig IPv6-addresse.',
    'json'     => ':attribute må være på JSON-format.',
    'lt'       => [
        'array'   => ':attribute må ha færre enn :value elementer.',
        'file'    => ':attribute må være mindre enn :value kilobyte.',
        'numeric' => ':attribute må være mindre enn :value.',
        'string'  => ':attribute må være mindre enn :value tegn.'
    ],
    'lte' => [
        'array'   => ':attribute må ikke ha flere enn :value elementer.',
        'file'    => ':attribute må være mindre enn eller lik :value kilobyte.',
        'numeric' => ':attribute må være mindre enn eller lik :value.',
        'string'  => ':attribute må være mindre enn eller lik :value tegn.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute må ikke ha flere enn :max elementer.',
        'file'    => ':attribute må ikke være større enn :max kilobyte.',
        'numeric' => ':attribute må ikke være større enn :max.',
        'string'  => ':attribute må ikke være lengre enn :max tegn.'
    ],
    'mimes'     => ':attribute må være en fil av typen: :values.',
    'mimetypes' => ':attribute må være en fil av typen: :values.',
    'min'       => [
        'array'   => ':attribute må ha minst :min elementer.',
        'file'    => ':attribute må være minst :min kilobyte.',
        'numeric' => ':attribute må være minst :min.',
        'string'  => ':attribute må være minst :min tegn.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Den valgte :attribute er ugyldig.',
    'not_regex'   => 'Formatet på :attribute er ugyldig.',
    'numeric'     => ':attribute må være et tall.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute må eksistere.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Formatet på :attribute er ugyldig.',
    'required'             => ':attribute må fylles ut.',
    'required_if'          => ':attribute må fylles ut når :other er :value.',
    'required_unless'      => ':attribute er påkrevd med mindre :other finnes blant verdiene :values.',
    'required_with'        => ':attribute må fylles ut når :values er utfylt.',
    'required_with_all'    => ':attribute er påkrevd når :values er oppgitt.',
    'required_without'     => ':attribute må fylles ut når :values ikke er utfylt.',
    'required_without_all' => ':attribute er påkrevd når ingen av :values er oppgitt.',
    'same'                 => ':attribute og :other må være like.',
    'size'                 => [
        'array'   => ':attribute må inneholde :size elementer.',
        'file'    => ':attribute må være :size kilobyte.',
        'numeric' => ':attribute må være :size.',
        'string'  => ':attribute må være :size tegn lang.'
    ],
    'starts_with' => ':attribute må begynne med en av følgende: :values',
    'string'      => ':attribute må være en tekststreng.',
    'timezone'    => ':attribute må være en gyldig tidssone.',
    'unique'      => ':attribute er allerede i bruk.',
    'uploaded'    => ':attribute kunne ikke lastes opp.',
    'url'         => 'Formatet på :attribute er ugyldig.',
    'uuid'        => ':attribute må være en gyldig UUID.'
];
