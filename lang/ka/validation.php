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
    'accepted'       => ':attribute უნდა იყოს მონიშნული.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute უნდა იყოს URL მისამართი.',
    'after'          => ':attribute უნდა იყოს :date-ის შემდეგ.',
    'after_or_equal' => ':attribute უნდა იყოს :date-ის შემდეგ ან მისი ტოლი.',
    'alpha'          => ':attribute უნდა შეიცავდეს მხოლოდ ასოებს.',
    'alpha_dash'     => ':attribute უნდა შეიცავდეს მხოლოდ ასოებს, რიცხვებს და ტირეებს.',
    'alpha_num'      => ':attribute უნდა შეიცავდეს მხოლოდ ასოებს და რიცხვებს.',
    'array'          => ':attribute უნდა იყოს მასივი.',
    'attributes'     => [
    ],
    'before'          => ':attribute უნდა იყოს :date-მდე.',
    'before_or_equal' => ':attribute უნდა იყოს :date-მდე ან მისი ტოლი.',
    'between'         => [
        'array'   => ':attribute-ის რაოდენობა უნდა იყოს :min-დან :max-მდე.',
        'file'    => ':attribute უნდა იყოს :min-სა და :max კილობაიტს შორის.',
        'numeric' => ':attribute უნდა იყოს :min-სა და :max-ს შორის.',
        'string'  => ':attribute უნდა იყოს :min-სა და :max სიმბოლოს შორის.'
    ],
    'boolean'          => ':attribute უნდა იყოს true, false, 0 ან 1.',
    'confirmed'        => ':attribute არ ემთხვევა დადასტურებას.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute შეიცავს თარიღის არასწორ ფორმატს.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute არ ემთხვევა თარიღის ფორმატს: :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute და :other არ უნდა ემთხვეოდეს ერთმანეთს.',
    'digits'         => ':attribute უნდა შედგებოდეს :digits ციფრისგან.',
    'digits_between' => ':attribute უნდა შედგებოდეს :min-დან :max ციფრამბდე.',
    'dimensions'     => ':attribute შეიცავს სურათის არასწორ ზომებს.',
    'distinct'       => ':attribute უნდა იყოს უნიკალური.',
    'email'          => ':attribute უნდა იყოს სწორი ელ.ფოსტა.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'ასეთი :attribute არ არსებობს.',
    'file'           => ':attribute უნდა იყოს ფაილი.',
    'filled'         => ':attribute აუცილებელია.',
    'gt'             => [
        'array'   => ':attribute უნდა შეიცავლდეს :value ელემენტზე მეტს.',
        'file'    => ':attribute უნდა იყოს :value კილობაიტზე მეტი.',
        'numeric' => ':attribute უნდა იყოს :value-ზე მეტი.',
        'string'  => ':attribute უნდა შეიცავდეს :value სიმბოლოზე მეტს.'
    ],
    'gte' => [
        'array'   => ':attribute უნდა შეიცავდეს მინიმუმ :value ელემენტს.',
        'file'    => ':attribute უნდა იყოს მინიმუმ :value კილობაიტი.',
        'numeric' => ':attribute უნდა იყოს მინიმუმ :value.',
        'string'  => ':attribute უნდა შეიცავდეს მინიმუმ :value სიმბოლოს.'
    ],
    'image'    => ':attribute უნდა იყოს სურათი.',
    'in'       => 'მითითებული :attribute არასწორია.',
    'in_array' => ':attribute უნდა არსებობდეს :other-ში.',
    'integer'  => ':attribute უნდა იყოს მთელი რიცხვი.',
    'ip'       => ':attribute უნდა იყოს IP მისამართი.',
    'ipv4'     => ':attribute უნდა იყოს IPv4 მისამართი.',
    'ipv6'     => ':attribute უნდა იყოს IPv6 მისამართი.',
    'json'     => ':attribute უნდა იყოს JSON ტიპის.',
    'lt'       => [
        'array'   => ':attribute უნდა შეიცავლდეს :value ელემენტზე ნაკლებს.',
        'file'    => ':attribute უნდა იყოს :value კილობაიტზე ნაკლები.',
        'numeric' => ':attribute უნდა იყოს :value-ზე ნაკლები.',
        'string'  => ':attribute უნდა შეიცავდეს :value სიმბოლოზე ნაკლებს.'
    ],
    'lte' => [
        'array'   => ':attribute უნდა შეიცავდეს მაქსიმუმ :value ელემენტს.',
        'file'    => ':attribute უნდა იყოს მაქსიმუმ :value კილობაიტი.',
        'numeric' => ':attribute უნდა იყოს მაქსიმუმ :value.',
        'string'  => ':attribute უნდა შეიცავდეს მაქსიმუმ :value სიმბოლოს.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute-ის რაოდენობა არ უნდა აღემატებოდეს :max-ს.',
        'file'    => ':attribute არ უნდა აღემატებოდეს :max კილობაიტს.',
        'numeric' => ':attribute არ უნდა აღემატებოდეს :max-ს.',
        'string'  => ':attribute არ უნდა აღემატებოდეს :max სიმბოლოს.'
    ],
    'mimes'     => ':attribute უნდა იყოს შემდეგი ტიპის: :values.',
    'mimetypes' => ':attribute უნდა იყოს შემდეგი ტიპის: :values.',
    'min'       => [
        'array'   => ':attribute უნდა იყოს მინიმუმ :min.',
        'file'    => ':attribute უნდა იყოს მინიმუმ :min კილობაიტი.',
        'numeric' => ':attribute უნდა იყოს მინიმუმ :min.',
        'string'  => ':attribute უნდა შეიცავდეს მინიმუმ :min სიმბოლოს.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'მითითებული :attribute არასწორია.',
    'not_regex'   => ':attribute არასწორ ფორმატშია.',
    'numeric'     => ':attribute უნდა იყოს რიცხვი.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute უნდა არსებობდეს, თუნდაც ცარიელი.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute არ ემთხვევა ფორმატს.',
    'required'             => ':attribute აუცილებელია.',
    'required_if'          => ':attribute აუცილებელია, თუ :other-ის მნიშვნელობა ემთხვევა :value-ს.',
    'required_unless'      => ':attribute აუცილებელია, თუ :values არ შეიცავს :other-ს.',
    'required_with'        => ':attribute აუცილებელია, თუ :values მითითებულია.',
    'required_with_all'    => ':attribute აუცილებელია, თუ :values მითითებულია.',
    'required_without'     => ':attribute აუცილებელია, თუ :values არ არის მითითებული.',
    'required_without_all' => ':attribute აუცილებელია, თუ :values არ არის მითითებული.',
    'same'                 => ':attribute და :other უნდა ემთხვეოდეს ერთმანეთს.',
    'size'                 => [
        'array'   => ':attribute უნდა შეიცავდეს :size ელემენტს.',
        'file'    => ':attribute უნდა იყოს :size კილობაიტი.',
        'numeric' => ':attribute უნდა იყოს :size-ის ტოლი.',
        'string'  => ':attribute უნდა შედგებოდეს :size სიმბოლოსგან.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute უნდა იყოს ტექსტი.',
    'timezone'    => ':attribute უნდა იყოს სასაათო სარტყელი.',
    'unique'      => 'ასეთი :attribute უკვე არსებობს.',
    'uploaded'    => ':attribute-ის ატვირთვა ვერ მოხერხდა.',
    'url'         => ':attribute უნდა იყოს URL მისამართი.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
