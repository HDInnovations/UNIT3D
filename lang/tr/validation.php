<?php
return [
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
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */
    'accepted' => ':attribute kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL değil.',
    'after' => ':attribute, :date\'den sonraki bir tarih olmalıdır.',
    'after_or_equal' => ':attribute, :date tarihinden sonra veya ona eşit bir tarih olmalıdır.',
    'alpha' => ':attribute yalnızca harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute yalnızca harf, rakam, tire ve alt çizgi içermelidir.',
    'alpha_num' => ':attribute yalnızca harf ve rakamlardan oluşmalıdır.',
    'array' => ':attribute bir dizi olmalıdır.',
    'before' => ':attribute, :date\'den önceki bir tarih olmalıdır.',
    'before_or_equal' => ':attribute, :date\'den önceki veya ona eşit bir tarih olmalıdır.',
    'between' => [
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'file' => ':attribute :min ile :max kilobayt arasında olmalıdır.',
        'string' => ':attribute :min ile :max karakter arasında olmalıdır.',
        'array' => ':attribute\'un :min ile :max arasında öğeleri olmalıdır.',
    ],
    'boolean' => ':attribute alanı true veya false olmalıdır.',
    'confirmed' => ':attribute onayı eşleşmiyor.',
    'date' => ':attribute geçerli bir tarih değil.',
    'date_equals' => ':attribute, :date değerine eşit bir tarih olmalıdır.',
    'date_format' => ':attribute, :format biçimiyle eşleşmiyor.',
    'different' => ':attribute ve :other farklı olmalıdır.',
    'digits' => ':attribute :digits rakamlarından oluşmalıdır.',
    'digits_between' => ':attribute :min ile :max arasında bir rakam olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutlarına sahip.',
    'distinct' => ':attribute alanı yinelenen bir değere sahip.',
    'email' => ':attribute geçerli bir eposta adresi olmalıdır.',
    'exists' => 'Seçili :attribute geçersiz.',
    'file' => ':attribute bir dosya olmalıdır.',
    'filled' => ':attribute alanının bir değeri olmalıdır.',
    'gt' => [
        'numeric' => ':attribute değeri :value değerinden büyük olmalıdır.',
        'file' => ':attribute değeri :value kilobayttan büyük olmalıdır.',
        'string' => ':attribute :value karakterlerinden büyük olmalıdır.',
        'array' => ':attribute\'un :value öğesinden daha fazlasına sahip olması gerekir.',
    ],
    'gte' => [
        'numeric' => ':attribute değeri :value değerinden büyük veya eşit olmalıdır.',
        'file' => ':attribute değeri :value kilobayttan büyük veya ona eşit olmalıdır.',
        'string' => ':attribute, :value karakterlerinden büyük veya eşit olmalıdır.',
        'array' => ':attribute :value veya daha fazla öğeye sahip olmalıdır.',
    ],
    'image' => ':attribute bir resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':attribute alanı :other içinde mevcut değil.',
    'integer' => ':attribute bir tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizesi olmalıdır.',
    'lt' => [
        'numeric' => ':attribute değeri :value değerinden küçük olmalıdır.',
        'file' => ':attribute değeri :value kilobayttan küçük olmalıdır.',
        'string' => ':attribute değeri :value karakterden küçük olmalıdır.',
        'array' => ':attribute öğesi :value değerinden az olmalıdır.',
    ],
    'lte' => [
        'numeric' => ':attribute değeri :value değerinden küçük veya eşit olmalıdır.',
        'file' => ':attribute değeri :value kilobayttan küçük veya ona eşit olmalıdır.',
        'string' => ':attribute, :value karakterinden küçük veya ona eşit olmalıdır.',
        'array' => ':attribute en fazla :value öğesi içermelidir.',
    ],
    'max' => [
        'numeric' => ':attribute değeri :max değerinden büyük olmamalıdır.',
        'file' => ':attribute :max kilobayttan büyük olmamalıdır.',
        'string' => ':attribute :max karakterden büyük olmamalıdır.',
        'array' => ':attribute en fazla :max öğeye sahip olmalıdır.',
    ],
    'mimes' => ':attribute dosyası :values türünde bir dosya olmalıdır.',
    'mimetypes' => ':attribute dosyası :values türünde bir dosya olmalıdır.',
    'min' => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'string' => ':attribute en az :min karakter uzunluğunda olmalıdır.',
        'array' => ':attribute en az :min ögeye sahip olmalıdır.',
    ],
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute biçimi geçersiz.',
    'numeric' => ':attribute bir sayı olmalıdır.',
    'present' => ':attribute alanı mevcut olmalıdır.',
    'regex' => ':attribute biçimi geçersiz.',
    'required' => ':attribute alanı zorunludur.',
    'required_if' => ':attribute alanı, :other değeri :value olduğunda zorunludur.',
    'required_unless' => ':attribute alanı, :other alanı :values içinde olmadığı sürece zorunludur.',
    'required_with' => ':attribute alanı, :values mevcut olduğunda gereklidir.',
    'required_with_all' => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_without' => ':attribute alanı, :values mevcut olmadığında gereklidir.',
    'required_without_all' => ':attribute alanı, :values değerlerinden hiçbiri olmadığında gereklidir.',
    'same' => ':attribute ve :other eşleşmelidir.',
    'size' => [
        'numeric' => ':attribute :size olmalıdır.',
        'file' => ':attribute :size kilobayt olmalıdır.',
        'string' => ':attribute :size karakter uzunluğunda olmalıdır.',
        'array' => ':attribute :size öğelerini içermelidir.',
    ],
    'starts_with' => ':attribute aşağıdakilerden biriyle başlamalıdır: :values.',
    'string' => ':attribute bir dize olmalıdır.',
    'timezone' => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique' => ':attribute zaten alınmış.',
    'uploaded' => ':attribute yüklenemedi.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',
    'custom' => [
        'attribute-name' => [
            /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
            'rule-name' => 'custom-message',
        ],
    ],
    'accepted_if' => ':attribute, :other değeri :value olduğunda kabul edilmelidir.',
    'current_password' => 'Şifre hatalı.',
    'declined' => ':attribute reddedilmelidir.',
    'declined_if' => ':attribute, :other ifadesi :value olduğunda reddedilmelidir.',
    'enum' => 'Seçilen :attribute geçersiz.',
    'ends_with' => ':attribute aşağıdakilerden biriyle bitmelidir: :values.',
    'mac_address' => ':attribute geçerli bir MAC adresi olmalıdır.',
    'multiple_of' => ':attribute, :value\'nun katı olmalıdır.',
    'password' => [
        'letters' => ':attribute en az bir harf içermelidir.',
        'mixed' => ':attribute en az bir büyük ve bir küçük harf içermelidir.',
        'numbers' => ':attribute en az bir sayı içermelidir.',
        'symbols' => ':attribute en az bir sembol içermelidir.',
        'uncompromised' => 'Belirtilen :attribute bir veri sızıntısında ortaya çıktı. Lütfen farklı bir :attribute seçin.',
    ],
    'prohibited' => ':attribute alanı yasaktır.',
    'prohibited_if' => ':attribute alanı, :other değeri :value olduğunda yasaktır.',
    'prohibited_unless' => ':attribute alanı, :other alanı :values içinde olmadığı sürece yasaktır.',
    'prohibits' => ':attribute alanı :other özelliğinin bulunmasını engeller.',
    'email_list' => 'Üzgünüz, bu eposta alan adının bu sitede kullanılmasına izin verilmiyor. Lütfen sitenin eposta beyaz listesine bakın.',
    'recaptcha' => 'Lütfen ReCaptcha\'yı tamamlayın.',
];
