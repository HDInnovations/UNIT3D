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
    'accepted'       => 'Isian :attribute harus diterima.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Isian :attribute bukan URL yang valid.',
    'after'          => 'Isian :attribute harus tanggal setelah :date.',
    'after_or_equal' => 'Isian :attribute harus berupa tanggal setelah atau sama dengan tanggal :date.',
    'alpha'          => 'Isian :attribute hanya boleh berisi huruf.',
    'alpha_dash'     => 'Isian :attribute hanya boleh berisi huruf, angka, dan strip.',
    'alpha_num'      => 'Isian :attribute hanya boleh berisi huruf dan angka.',
    'array'          => 'Isian :attribute harus berupa sebuah array.',
    'attributes'     => [
    ],
    'before'          => 'Isian :attribute harus tanggal sebelum :date.',
    'before_or_equal' => 'Isian :attribute harus berupa tanggal sebelum atau sama dengan tanggal :date.',
    'between'         => [
        'array'   => 'Isian :attribute harus antara :min dan :max item.',
        'file'    => 'Bidang :attribute harus antara :min dan :max kilobyte.',
        'numeric' => 'Isian :attribute harus antara :min dan :max.',
        'string'  => 'Isian :attribute harus antara :min dan :max karakter.'
    ],
    'boolean'          => 'Isian :attribute harus berupa true atau false',
    'confirmed'        => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Isian :attribute bukan tanggal yang valid.',
    'date_equals'    => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'    => 'Isian :attribute tidak cocok dengan format :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Isian :attribute dan :other harus berbeda.',
    'digits'         => 'Isian :attribute harus berupa angka :digits.',
    'digits_between' => 'Isian :attribute harus antara angka :min dan :max.',
    'dimensions'     => 'Bidang :attribute tidak memiliki dimensi gambar yang valid.',
    'distinct'       => 'Bidang isian :attribute memiliki nilai yang duplikat.',
    'email'          => 'Isian :attribute harus berupa alamat surel yang valid.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Isian :attribute yang dipilih tidak valid.',
    'file'           => 'Bidang :attribute harus berupa sebuah berkas.',
    'filled'         => 'Isian :attribute harus memiliki nilai.',
    'gt'             => [
        'array'   => 'Isian :attribute harus lebih dari :value item.',
        'file'    => 'Bidang :attribute harus lebih besar dari :value kilobyte.',
        'numeric' => 'Isian :attribute harus lebih besar dari :value.',
        'string'  => 'Isian :attribute harus lebih besar dari :value karakter.'
    ],
    'gte' => [
        'array'   => 'Isian :attribute harus mempunyai :value item atau lebih.',
        'file'    => 'Bidang :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'numeric' => 'Isian :attribute harus lebih besar dari atau sama dengan :value.',
        'string'  => 'Isian :attribute harus lebih besar dari atau sama dengan :value karakter.'
    ],
    'image'    => 'Isian :attribute harus berupa gambar.',
    'in'       => 'Isian :attribute yang dipilih tidak valid.',
    'in_array' => 'Bidang isian :attribute tidak terdapat dalam :other.',
    'integer'  => 'Isian :attribute harus merupakan bilangan bulat.',
    'ip'       => 'Isian :attribute harus berupa alamat IP yang valid.',
    'ipv4'     => 'Isian :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'     => 'Isian :attribute harus berupa alamat IPv6 yang valid.',
    'json'     => 'Isian :attribute harus berupa JSON string yang valid.',
    'lt'       => [
        'array'   => 'Isian :attribute harus kurang dari :value item.',
        'file'    => 'Bidang :attribute harus kurang dari :value kilobyte.',
        'numeric' => 'Isian :attribute harus kurang dari :value.',
        'string'  => 'Isian :attribute harus kurang dari :value karakter.'
    ],
    'lte' => [
        'array'   => 'Isian :attribute harus tidak lebih dari :value item.',
        'file'    => 'Bidang :attribute harus kurang dari atau sama dengan :value kilobyte.',
        'numeric' => 'Isian :attribute harus kurang dari atau sama dengan :value.',
        'string'  => 'Isian :attribute harus kurang dari atau sama dengan :value karakter.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Isian :attribute seharusnya tidak lebih dari :max item.',
        'file'    => 'Bidang :attribute seharusnya tidak lebih dari :max kilobyte.',
        'numeric' => 'Isian :attribute seharusnya tidak lebih dari :max.',
        'string'  => 'Isian :attribute seharusnya tidak lebih dari :max karakter.'
    ],
    'mimes'     => 'Isian :attribute harus dokumen berjenis : :values.',
    'mimetypes' => 'Isian :attribute harus dokumen berjenis : :values.',
    'min'       => [
        'array'   => 'Isian :attribute harus minimal :min item.',
        'file'    => 'Bidang :attribute harus minimal :min kilobyte.',
        'numeric' => 'Isian :attribute harus minimal :min.',
        'string'  => 'Isian :attribute harus minimal :min karakter.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Isian :attribute yang dipilih tidak valid.',
    'not_regex'   => 'Format isian :attribute tidak valid.',
    'numeric'     => 'Isian :attribute harus berupa angka.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Bidang isian :attribute wajib ada.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Format isian :attribute tidak valid.',
    'required'             => 'Bidang isian :attribute wajib diisi.',
    'required_if'          => 'Bidang isian :attribute wajib diisi bila :other adalah :value.',
    'required_unless'      => 'Bidang isian :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => 'Bidang isian :attribute wajib diisi bila terdapat :values.',
    'required_with_all'    => 'Bidang isian :attribute wajib diisi bila terdapat :values.',
    'required_without'     => 'Bidang isian :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Bidang isian :attribute wajib diisi bila tidak terdapat ada :values.',
    'same'                 => 'Isian :attribute dan :other harus sama.',
    'size'                 => [
        'array'   => 'Isian :attribute harus mengandung :size item.',
        'file'    => 'Bidang :attribute harus berukuran :size kilobyte.',
        'numeric' => 'Isian :attribute harus berukuran :size.',
        'string'  => 'Isian :attribute harus berukuran :size karakter.'
    ],
    'starts_with' => ':attribute harus dimulai dengan salah satu dari berikut ini: :values',
    'string'      => 'Isian :attribute harus berupa string.',
    'timezone'    => 'Isian :attribute harus berupa zona waktu yang valid.',
    'unique'      => 'Isian :attribute sudah ada sebelumnya.',
    'uploaded'    => 'Isian :attribute gagal diunggah.',
    'url'         => 'Format isian :attribute tidak valid.',
    'uuid'        => ':attribute harus UUID yang valid.'
];
