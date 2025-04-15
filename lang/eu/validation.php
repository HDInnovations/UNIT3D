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
    'accepted'       => ':attribute onartu beharra dago.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute ez da baliozko URL bat.',
    'after'          => ':attribute :date osteko data izan behar da.',
    'after_or_equal' => ':attribute :date osteko data edo data bera izan behar da.',
    'alpha'          => ':attribute hizkiak besterik ezin ditu izan.',
    'alpha_dash'     => ':attribute hizkiak, zenbakiak eta marrak besterik ezin ditu izan.',
    'alpha_num'      => ':attribute hizkiak eta zenbakiak besterik ezin ditu izan.',
    'array'          => ':attribute bilduma izan behar da.',
    'attributes'     => [
    ],
    'before'          => ':attribute :date aurreko data izan behar da.',
    'before_or_equal' => ':attribute :date aurreko data edo data bera izan behar da.',
    'between'         => [
        'array'   => ':attribute-(e)k :min eta :max arteko elementu kopurua izan behar du.',
        'file'    => ':attribute-(e)k :min eta :max kilobyte arteko pisua izan behar du.',
        'numeric' => ':attribute :min eta :max artean egon behar da.',
        'string'  => ':attribute-(e)k :min eta :max karaktere artean izan behar ditu.'
    ],
    'boolean'          => ':attribute-(r)en balioa egia edo gezurra izan behar da.',
    'confirmed'        => ':attribute-(r)en berrespena ez dator bat.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute ez da baliozko data.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute datak ez du :format formatua.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute eta :other ezberdinak izan behar dira.',
    'digits'         => ':attribute-(e)k :digits digitu eduki behar ditu.',
    'digits_between' => ':attribute-(e)k :min eta :max arteko digitu kopurua eduki behar du.',
    'dimensions'     => ':attribute irudiaren neurriak baliogabeak dira.',
    'distinct'       => ':attribute-(e)k bikoiztutako balioa dauka.',
    'email'          => ':attribute baliozko helbide elektronikoa izan behar da.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute baliogabea da.',
    'file'           => ':attribute fitxategi bat izan behar da.',
    'filled'         => ':attribute derrigorrezkoa da.',
    'gt'             => [
        'array'   => ':attribute-(e)k :value elementu baino gehiago izan behar ditu.',
        'file'    => ':attribute-(e)k :value kilobyte baino handiagoa izan behar du.',
        'numeric' => ':attribute-(e)k :value baino handiagoa izan behar du.',
        'string'  => ':attribute-(e)k :value karaktere baino gehiago izan behar ditu.'
    ],
    'gte' => [
        'array'   => ':attribute-(e)k :value elementu edo gehiago izan behar ditu.',
        'file'    => ':attribute-(e)k :value kilobyte edo gehiago izan behar ditu.',
        'numeric' => ':attribute-(e)k :value baino handiagoa edo berdina izan behar du.',
        'string'  => ':attribute-(e)k :value karaktere edo gehiago izan behar ditu.'
    ],
    'image'    => ':attribute irudi bat izan behar da.',
    'in'       => ':attribute baliogabea da.',
    'in_array' => ':attribute ez da existizen :other-(e)n.',
    'integer'  => ':attribute zenbaki osoa izan behar da.',
    'ip'       => ':attribute baliozko IP helbidea izan behar da.',
    'ipv4'     => ':attribute baliozko IPv4 helbidea izan behar da.',
    'ipv6'     => ':attribute baliozko IPv6 helbidea izan behar da.',
    'json'     => ':attribute baliozko JSON karaktere-katea izan behar da.',
    'lt'       => [
        'array'   => ':attribute-(e)k :value elementu baino gutxiago izan behar ditu.',
        'file'    => ':attribute-(e)k :value kilobyte baino txikiagoa izan behar du.',
        'numeric' => ':attribute-(e)k :value baino txikiagoa izan behar du.',
        'string'  => ':attribute-(e)k :value karaktere baino gutxiago izan behar ditu.'
    ],
    'lte' => [
        'array'   => ':attribute-(e)k :value elementu edo gutxiago izan behar ditu.',
        'file'    => ':attribute-(e)k :value kilobyte edo gutxiago izan behar ditu.',
        'numeric' => ':attribute-(e)k :value baino txikiagoa edo berdina izan behar du.',
        'string'  => ':attribute-(e)k :value karaktere edo gutxiago izan behar ditu.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute-(e)k ezin du :max elementu baino gehiago eduki.',
        'file'    => ':attribute ezin da :max kilobyte baino handiagoa izan.',
        'numeric' => ':attribute ezin da :max baino handiagoa izan.',
        'string'  => ':attribute-(e)k ezin du :max karaktere baino gehiago eduki.'
    ],
    'mimes'     => ':attribute :values motako fitxategia izan behar da.',
    'mimetypes' => ':attribute :values motako fitxategia izan behar da.',
    'min'       => [
        'array'   => ':attribute-(e)k gutxienez :min elementu izan behar ditu.',
        'file'    => ':attribute-(e)k gutxienez :min kilobyte izan behar ditu.',
        'numeric' => ':attribute-(e)k gutxienez :min-(e)ko tamaina izan behar du.',
        'string'  => ':attribute-(e)k gutxienez :min karaktere izan behar ditu.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute baliogabea da.',
    'not_regex'   => ':attribute formatua baliogabea da.',
    'numeric'     => ':attribute zenbakizkoa izan behar da.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute ezin da hutsik egon.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute baliogabea da.',
    'required'             => ':attribute derrigorrezkoa da.',
    'required_if'          => ':attribute derrigorrezkoa da :other :value denean.',
    'required_unless'      => ':attribute derrigorrezkoa da :other :values-(e)n egon ezean.',
    'required_with'        => ':attribute derrigorrezkoa da :values dagoenean.',
    'required_with_all'    => ':attribute derrigorrezkoa da :values daudenean.',
    'required_without'     => ':attribute derrigorrezkoa da :values ez dagoenean.',
    'required_without_all' => ':attribute derrigorrezkoa da :values ez daudenean.',
    'same'                 => ':attribute eta :other bat etorri behar dira.',
    'size'                 => [
        'array'   => ':attribute-(e)k :size elementu izan behar ditu.',
        'file'    => ':attribute-(e)k :size kilobyte izan behar behar ditu.',
        'numeric' => ':attribute-(e)k :size tamaina izan behar du.',
        'string'  => ':attribute-(e)k :size karaktere izan behar ditu.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute karaktere-katea izan behar da.',
    'timezone'    => ':attribute baliozko ordu-eremua izan behar da.',
    'unique'      => ':attribute jadanik erregistratua izan da.',
    'uploaded'    => ':attribute kargatzerakoan huts egin du.',
    'url'         => ':attribute-(r)en formatua baliogabea da.',
    'uuid'        => ':attribute-(e)k baliozko UUIDa izan behar du.'
];
