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
    'accepted'       => ':attribute গ্রহণ করা আবশ্যক।',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'এই :attribute একটি বৈধ URL নয়।',
    'after'          => ':date অবশ্যই :attribute এর পরের একটি তারিখ হতে হবে।',
    'after_or_equal' => ':attribute টি অবশ্যই :date এর সাথে মিল অথবা এর পরের একটি তারিখ হতে হবে।',
    'alpha'          => ':attribute শুধুমাত্র অক্ষর থাকতে পারে।',
    'alpha_dash'     => ':attribute শুধুমাত্র অক্ষর, সংখ্যা, এবং ড্যাশ থাকতে পারে।',
    'alpha_num'      => ':attribute শুধুমাত্র বর্ণ ও সংখ্যা থাকতে পারে।',
    'array'          => ':attribute একটি অ্যারে হতে হবে।',
    'attributes'     => [
    ],
    'before'          => ':date অবশ্যই :attribute এর আগের একটি তারিখ হতে হবে।',
    'before_or_equal' => ':attribute টি অবশ্যই :date এর সাথে মিল অথবা এর আগের একটি তারিখ হতে হবে।',
    'between'         => [
        'array'   => ':min এবং :max আইটেম :attribute মধ্যে হতে হবে।',
        'file'    => ':min এবং :max কিলোবাইট :attribute মধ্যে হতে হবে।',
        'numeric' => ':min এবং :max :attribute মধ্যে হতে হবে।',
        'string'  => ':min এবং :max অক্ষর :attribute মধ্যে হতে হবে।'
    ],
    'boolean'          => ':attribute স্থানে  সত্য বা মিথ্যা হতে হবে।',
    'confirmed'        => ':attribute নিশ্চিতকরণ এর  সাথে মিলছে না।',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute একটি বৈধ তারিখ নয়।',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute, :format এর সাথে বিন্যাস মিলছে না।',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute এবং :other আলাদা হতে হবে।',
    'digits'         => ':attribute :digits অবশ্যই একটি সংখ্যার ডিজিট হতে হবে।',
    'digits_between' => ':attribute অবশ্যই :min এবং :max ডিজিট এর মধ্যে হতে হবে।',
    'dimensions'     => ':attribute অবৈধ ইমেজ মাত্রা রয়েছে।',
    'distinct'       => ':attribute এর স্থানে একটি নকল মান আছে।',
    'email'          => ':attribute একটি বৈধ ইমেইল ঠিকানা হতে হবে।',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'নির্বাচিত :attribute টি অবৈধ।',
    'file'           => ':attribute একটি ফাইল হতে হবে।',
    'filled'         => ':attribute স্থানটি পূরণ করতে হবে।',
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
    'image'    => ':attribute একটি ইমেজ হতে হবে।',
    'in'       => 'নির্বাচিত :attribute টি অবৈধ।',
    'in_array' => ':attribute উপাদানটি :other এ খুঁজে পাওয়া যায়নি।.',
    'integer'  => ':attribute একটি পূর্ণসংখ্যা হতে হবে।',
    'ip'       => ':attribute একটি বৈধ  IP address হতে হবে।',
    'ipv4'     => ':attribute টি একটি বৈধ IPv4 address হতে হবে।',
    'ipv6'     => ':attribute টি একটি বৈধ IPv6 address হতে হবে।',
    'json'     => ':attribute একটি বৈধ JSON স্ট্রিং হতে হবে।',
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
        'array'   => ':attribute এর মান :max টি উপাদানের চেয়ে বড় হতে পারেনা।',
        'file'    => ':attribute এর মান :max কিলোবাইট এর চেয়ে বড় হতে পারেনা।',
        'numeric' => ' :attribute এর মান :max এর চেয়ে বড় হতে পারেনা।',
        'string'  => ':attribute এর মান :max অক্ষর এর চেয়ে বড় হতে পারেনা।'
    ],
    'mimes'     => ':attribute এর একটি ফাইল হতে হবে: :values।',
    'mimetypes' => ':attribute এর একটি ফাইল হতে হবে: :values।',
    'min'       => [
        'array'   => ':attribute অবশ্যই :min উপাদানের চেয়ে ছোট হতে হবে।',
        'file'    => ':attribute অবশ্যই :min কিলোবাইট এর চেয়ে ছোট হতে হবে।',
        'numeric' => ':attribute অবশ্যই :min এর চেয়ে ছোট হতে হবে।',
        'string'  => ':attribute অবশ্যই :min অক্ষর এর চেয়ে ছোট হতে হবে।'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'নির্বাচিত :attribute অবৈধ।',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute একটি সংখ্যা হতে হবে।',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute ক্ষেত্র উপস্থিত থাকা আবশ্যক।',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute বিন্যাস অবৈধ।',
    'required'             => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক।',
    'required_if'          => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যেখানে :other হল :value।',
    'required_unless'      => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যদি না :other, :values তে উপস্থিত থাকে।',
    'required_with'        => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যখন  :values উপস্থিত।',
    'required_with_all'    => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যখন :values উপস্থিত।',
    'required_without'     => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যখন :values অনুপস্থিত।',
    'required_without_all' => ':attribute স্থানটি পূরণ করা বাধ্যতামূলক যখন সকল :values অনুপস্থিত।',
    'same'                 => ':attribute এবং :other অবশ্যই মিলতে হবে।',
    'size'                 => [
        'array'   => ':attribute অবশ্যই :size আইটেম হতে হবে।',
        'file'    => ':attribute অবশ্যই :size কিলোবাইট হতে হবে।',
        'numeric' => ':attribute অবশ্যই :size হতে হবে।',
        'string'  => ':attribute অবশ্যই :size অক্ষর হতে হবে।'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute একটি স্ট্রিং হতে হবে।',
    'timezone'    => ':attribute একটি বৈধ সময় অঞ্চল হতে হবে।',
    'unique'      => ':attribute ইতিমধ্যেই নেওয়া হয়েছে।',
    'uploaded'    => ':attribute আপলোড করতে ব্যর্থ হয়েছে।',
    'url'         => ':attribute বিন্যাস অবৈধ।',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
