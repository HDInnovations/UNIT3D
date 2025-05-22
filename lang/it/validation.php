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
    'accepted'       => ':attribute deve essere accettato.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute non è un URL valido.',
    'after'          => ':attribute deve essere una data successiva al :date.',
    'after_or_equal' => ':attribute deve essere una data successiva o uguale al :date.',
    'alpha'          => ':attribute può contenere solo lettere.',
    'alpha_dash'     => ':attribute può contenere solo lettere, numeri e trattini.',
    'alpha_num'      => ':attribute può contenere solo lettere e numeri.',
    'array'          => ':attribute deve essere un array.',
    'attributes'     => [
    ],
    'before'          => ':attribute deve essere una data precedente al :date.',
    'before_or_equal' => ':attribute deve essere una data precedente o uguale al :date.',
    'between'         => [
        'array'   => ':attribute deve avere tra :min - :max elementi.',
        'file'    => ':attribute deve trovarsi tra :min - :max kilobyte.',
        'numeric' => ':attribute deve trovarsi tra :min - :max.',
        'string'  => ':attribute di :min - :max caratteri'
    ],
    'boolean'          => 'Il campo :attribute deve essere vero o falso.',
    'confirmed'        => 'Il campo di conferma per :attribute non coincide.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute non è una data valida.',
    'date_equals'    => ':attribute deve essere una data e uguale a :date.',
    'date_format'    => ':attribute non coincide con il formato :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute e :other devono essere differenti.',
    'digits'         => ':attribute deve essere di :digits cifre.',
    'digits_between' => ':attribute deve essere tra :min e :max cifre.',
    'dimensions'     => 'Le dimensioni dell\'immagine di :attribute non sono valide.',
    'distinct'       => ':attribute contiene un valore duplicato.',
    'email'          => ':attribute non è valido.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute selezionato non è valido.',
    'file'           => ':attribute deve essere un file.',
    'filled'         => 'Il campo :attribute deve contenere un valore.',
    'gt'             => [
        'array'   => ':attribute deve contenere più di :value elementi.',
        'file'    => ':attribute deve essere maggiore di :value kilobyte.',
        'numeric' => ':attribute deve essere maggiore di :value.',
        'string'  => ':attribute deve contenere più di :value caratteri.'
    ],
    'gte' => [
        'array'   => ':attribute deve contenere un numero di elementi uguale o maggiore di :value.',
        'file'    => ':attribute deve essere uguale o maggiore di :value kilobyte.',
        'numeric' => ':attribute deve essere uguale o maggiore di :value.',
        'string'  => ':attribute deve contenere un numero di caratteri uguale o maggiore di :value.'
    ],
    'image'    => ':attribute deve essere un\'immagine.',
    'in'       => ':attribute selezionato non è valido.',
    'in_array' => 'Il valore del campo :attribute non esiste in :other.',
    'integer'  => ':attribute deve essere un numero intero.',
    'ip'       => ':attribute deve essere un indirizzo IP valido.',
    'ipv4'     => ':attribute deve essere un indirizzo IPv4 valido.',
    'ipv6'     => ':attribute deve essere un indirizzo IPv6 valido.',
    'json'     => ':attribute deve essere una stringa JSON valida.',
    'lt'       => [
        'array'   => ':attribute deve contenere meno di :value elementi.',
        'file'    => ':attribute deve essere minore di :value kilobyte.',
        'numeric' => ':attribute deve essere minore di :value.',
        'string'  => ':attribute deve contenere meno di :value caratteri.'
    ],
    'lte' => [
        'array'   => ':attribute deve contenere un numero di elementi minore o uguale a :value.',
        'file'    => ':attribute deve essere minore o uguale a :value kilobyte.',
        'numeric' => ':attribute deve essere minore o uguale a :value.',
        'string'  => ':attribute deve contenere un numero di caratteri minore o uguale a :value.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute non può avere più di :max elementi.',
        'file'    => ':attribute non può essere superiore a :max kilobyte.',
        'numeric' => ':attribute non può essere superiore a :max.',
        'string'  => ':attribute non può contenere più di :max caratteri.'
    ],
    'mimes'     => ':attribute deve essere del tipo: :values.',
    'mimetypes' => ':attribute deve essere del tipo: :values.',
    'min'       => [
        'array'   => ':attribute deve avere almeno :min elementi.',
        'file'    => ':attribute deve essere almeno di :min kilobyte.',
        'numeric' => ':attribute deve essere almeno :min.',
        'string'  => ':attribute deve contenere almeno :min caratteri.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Il valore selezionato per :attribute non è valido.',
    'not_regex'   => 'Il formato di :attribute non è valido.',
    'numeric'     => ':attribute deve essere un numero.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Il campo :attribute deve essere presente.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Il formato del campo :attribute non è valido.',
    'required'             => 'Il campo :attribute è richiesto.',
    'required_if'          => 'Il campo :attribute è richiesto quando :other è :value.',
    'required_unless'      => 'Il campo :attribute è richiesto a meno che :other sia in :values.',
    'required_with'        => 'Il campo :attribute è richiesto quando :values è presente.',
    'required_with_all'    => 'Il campo :attribute è richiesto quando :values sono presenti.',
    'required_without'     => 'Il campo :attribute è richiesto quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute è richiesto quando nessuno di :values è presente.',
    'same'                 => ':attribute e :other devono coincidere.',
    'size'                 => [
        'array'   => ':attribute deve contenere :size elementi.',
        'file'    => ':attribute deve essere :size kilobyte.',
        'numeric' => ':attribute deve essere :size.',
        'string'  => ':attribute deve contenere :size caratteri.'
    ],
    'starts_with' => ':attribute deve iniziare con uno dei seguenti: :values',
    'string'      => ':attribute deve essere una stringa.',
    'timezone'    => ':attribute deve essere una zona valida.',
    'unique'      => ':attribute già utilizzato',
    'uploaded'    => ':attribute non è stato caricato.',
    'url'         => 'Il formato del campo :attribute non è valido.',
    'uuid'        => ':attribute deve essere un UUID valido.'
];
