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
    'accepted'       => ':attribute mesti diterima pakai.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute bukan URL yang sah.',
    'after'          => ':attribute mesti tarikh selepas :date.',
    'after_or_equal' => ':attribute mesti tarikh selepas atau sama dengan :date.',
    'alpha'          => ':attribute hanya boleh mengandungi huruf.',
    'alpha_dash'     => ':attribute boleh mengandungi huruf, nombor, dan sengkang.',
    'alpha_num'      => ':attribute boleh mengandungi huruf dan nombor.',
    'array'          => ':attribute mesti jujukan.',
    'attributes'     => [
    ],
    'before'          => ':attribute mesti tarikh sebelum :date.',
    'before_or_equal' => ':attribute mesti tarikh sebelum atau sama dengan :date.',
    'between'         => [
        'array'   => ':attribute mesti mengandungi antara :min dan :max perkara.',
        'file'    => ':attribute mesti mengandungi antara :min dan :max kilobait.',
        'numeric' => ':attribute mesti mengandungi antara :min dan :max.',
        'string'  => ':attribute mesti mengandungi antara :min dan :max aksara.'
    ],
    'boolean'          => ':attribute mesti benar atau salah.',
    'confirmed'        => ':attribute pengesahan yang tidak sepadan.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute bukan tarikh yang sah.',
    'date_equals'    => ':attribute mesti tarikh sama dengan :date.',
    'date_format'    => ':attribute tidak mengikut format :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute dan :other mesti berlainan.',
    'digits'         => ':attribute mesti :digits.',
    'digits_between' => ':attribute mesti mengandungi antara :min dan :max digits.',
    'dimensions'     => ':attribute tidak sah',
    'distinct'       => ':attribute adalah nilai yang berulang',
    'email'          => ':attribute tidak sah.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute tidak sah.',
    'file'           => ':attribute mesti fail yang sah.',
    'filled'         => ':attribute diperlukan.',
    'gt'             => [
        'array'   => ':attribute mesti mengandungi lebih daripada :value perkara.',
        'file'    => ':attribute mesti melebihi :value kilobait.',
        'numeric' => ':attribute mesti melebihi :value.',
        'string'  => ':attribute mesti melebihi :value aksara.'
    ],
    'gte' => [
        'array'   => ':attribute mesti mengandungi :value perkara atau lebih.',
        'file'    => ':attribute mesti melebihi atau bersamaan :value kilobait.',
        'numeric' => ':attribute mesti melebihi atau bersamaan :value.',
        'string'  => ':attribute mesti melebihi atau bersamaan :value aksara.'
    ],
    'image'    => ':attribute mesti imej.',
    'in'       => ':attribute tidak sah.',
    'in_array' => ':attribute tidak wujud dalam :other.',
    'integer'  => ':attribute mesti integer.',
    'ip'       => ':attribute mesti alamat IP yang sah.',
    'ipv4'     => ':attribute mesti alamat IPv4 yang sah.',
    'ipv6'     => ':attribute mesti alamat IPv6 yang sah',
    'json'     => ':attribute mesti JSON yang sah.',
    'lt'       => [
        'array'   => ':attribute mesti mengandungi kurang daripada :value perkara.',
        'file'    => ':attribute mesti kurang daripada :value kilobait.',
        'numeric' => ':attribute mesti kurang daripada :value.',
        'string'  => ':attribute mesti kurang daripada :value aksara.'
    ],
    'lte' => [
        'array'   => ':attribute mesti mengandungi kurang daripada atau bersamaan dengan :value perkara.',
        'file'    => ':attribute mesti kurang daripada atau bersamaan dengan :value kilobait.',
        'numeric' => ':attribute mesti kurang daripada atau bersamaan dengan :value.',
        'string'  => ':attribute mesti kurang daripada atau bersamaan dengan :value aksara.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Jumlah :attribute mesti tidak melebihi :max perkara.',
        'file'    => 'Jumlah :attribute mesti tidak melebihi :max kilobait.',
        'numeric' => 'Jumlah :attribute mesti tidak melebihi :max.',
        'string'  => 'Jumlah :attribute mesti tidak melebihi :max aksara.'
    ],
    'mimes'     => ':attribute mesti fail type: :values.',
    'mimetypes' => ':attribute mesti fail type: :values.',
    'min'       => [
        'array'   => 'Jumlah :attribute mesti sekurang-kurangnya :min perkara.',
        'file'    => 'Jumlah :attribute mesti sekurang-kurangnya :min kilobait.',
        'numeric' => 'Jumlah :attribute mesti sekurang-kurangnya :min.',
        'string'  => 'Jumlah :attribute mesti sekurang-kurangnya :min aksara.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute tidak sah.',
    'not_regex'   => 'Format :attribute adalah tidak sah.',
    'numeric'     => ':attribute mesti nombor.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute mesti wujud.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Format :attribute tidak sah.',
    'required'             => 'Ruangan :attribute diperlukan.',
    'required_if'          => 'Ruangan :attribute diperlukan bila :other sama dengan :value.',
    'required_unless'      => 'Ruangan :attribute diperlukan sekiranya :other ada dalam :values.',
    'required_with'        => 'Ruangan :attribute diperlukan bila :values wujud.',
    'required_with_all'    => 'Ruangan :attribute diperlukan bila :values wujud.',
    'required_without'     => 'Ruangan :attribute diperlukan bila :values tidak wujud.',
    'required_without_all' => 'Ruangan :attribute diperlukan bila kesemua :values wujud.',
    'same'                 => 'Ruangan :attribute dan :other mesti sepadan.',
    'size'                 => [
        'array'   => 'Saiz :attribute mesti mengandungi :size perkara.',
        'file'    => 'Saiz :attribute mesti :size kilobait.',
        'numeric' => 'Saiz :attribute mesti :size.',
        'string'  => 'Saiz :attribute mesti :size aksara.'
    ],
    'starts_with' => ':attribute mesti bermula dengan salah satu dari: :values',
    'string'      => ':attribute mesti aksara.',
    'timezone'    => ':attribute mesti zon masa yang sah.',
    'unique'      => ':attribute telah wujud.',
    'uploaded'    => ':attribute gagal dimuat naik.',
    'url'         => ':attribute format tidak sah.',
    'uuid'        => ':attribute mesti UUID yang sah.'
];
