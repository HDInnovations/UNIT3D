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
    | שורות שגיאות אימות
    |--------------------------------------------------------------------------
    |
    | השורות הבאות מכילות את הודעות השגיאה המובנות המשמשות על ידי מחלקת
    | האימות. לחלק מהכללים יש גרסאות מרובות כמו חוקי גודל. אתם מוזמנים
    | לשנות כל הודעה לפי הצורך כדי להתאים את ההודעות ליישום שלכם.
    |
    */
    'accepted' => ':attribute חייב להתקבל.',
    'accepted_if' => ':attribute חייב להתקבל כאשר :other הוא :value.',
    'active_url' => ':attribute אינו URL חוקי.',
    'after' => ':attribute חייב להיות תאריך אחרי :date.',
    'after_or_equal' => ':attribute חייב להיות תאריך אחרי או שווה ל :date.',
    'alpha' => ':attribute יכול להכיל רק אותיות.',
    'alpha_dash' => ':attribute יכול להכיל רק אותיות, מספרים, מקפים וקווים תחתונים.',
    'alpha_num' => ':attribute יכול להכיל רק אותיות ומספרים.',
    'array' => ':attribute חייב להיות מערך.',
    'before' => ':attribute חייב להיות תאריך לפני :date.',
    'before_or_equal' => ':attribute חייב להיות תאריך לפני או שווה ל :date.',
    'between' => [
        'numeric' => ':attribute חייב להיות בין :min ל- :max.',
        'file' => ':attribute חייב להיות בין :min ל- :max קילובייט.',
        'string' => ':attribute חייב להיות בין :min ל- :max תווים.',
        'array' => ':attribute חייב לכלול בין :min ל- :max פריטים.',
    ],
    'boolean' => 'שדה :attribute חייב להיות אמת או שקר.',
    'confirmed' => 'אישור :attribute לא תואם.',
    'current_password' => 'הסיסמה שגויה.',
    'date' => ':attribute אינו תאריך חוקי.',
    'date_equals' => ':attribute חייב להיות תאריך השווה ל- :date.',
    'date_format' => ':attribute אינו תואם את הפורמט :format.',
    'declined' => ':attribute חייב להידחות.',
    'declined_if' => ':attribute חייב להידחות כאשר :other הוא :value.',
    'different' => ':attribute ו- :other חייבים להיות שונים.',
    'digits' => ':attribute חייב להיות :digits ספרות.',
    'digits_between' => ':attribute חייב להיות בין :min ל- :max ספרות.',
    'dimensions' => ':attribute בעל ממדי תמונה לא תקינים.',
    'distinct' => 'שדה :attribute מכיל ערך כפול.',
    'email' => ':attribute חייב להיות כתובת אימייל חוקית.',
    'ends_with' => ':attribute חייב להסתיים באחד מהבאים: :values.',
    'enum' => ':attribute הנבחר אינו חוקי.',
    'exists' => ':attribute הנבחר אינו חוקי.',
    'file' => ':attribute חייב להיות קובץ.',
    'filled' => 'שדה :attribute חייב להכיל ערך.',
    'gt' => [
        'numeric' => ':attribute חייב להיות גדול מ- :value.',
        'file' => ':attribute חייב להיות גדול מ- :value קילובייט.',
        'string' => ':attribute חייב להיות גדול מ- :value תווים.',
        'array' => ':attribute חייב להכיל יותר מ- :value פריטים.',
    ],
    'gte' => [
        'numeric' => ':attribute חייב להיות גדול או שווה ל- :value.',
        'file' => ':attribute חייב להיות גדול או שווה ל- :value קילובייט.',
        'string' => ':attribute חייב להיות גדול או שווה ל- :value תווים.',
        'array' => ':attribute חייב להכיל לפחות :value פריטים.',
    ],
    'image' => ':attribute חייב להיות תמונה.',
    'in' => ':attribute הנבחר אינו חוקי.',
    'in_array' => 'שדה :attribute אינו קיים ב- :other.',
    'integer' => ':attribute חייב להיות מספר שלם.',
    'ip' => ':attribute חייב להיות כתובת IP חוקית.',
    'ipv4' => ':attribute חייב להיות כתובת IPv4 חוקית.',
    'ipv6' => ':attribute חייב להיות כתובת IPv6 חוקית.',
    'mac_address' => ':attribute חייב להיות כתובת MAC חוקית.',
    'json' => ':attribute חייב להיות מחרוזת JSON חוקית.',
    'lt' => [
        'numeric' => ':attribute חייב להיות קטן מ- :value.',
        'file' => ':attribute חייב להיות קטן מ- :value קילובייט.',
        'string' => ':attribute חייב להיות קטן מ- :value תווים.',
        'array' => ':attribute חייב לכלול פחות מ- :value פריטים.',
    ],
    'lte' => [
        'numeric' => ':attribute חייב להיות קטן או שווה ל- :value.',
        'file' => ':attribute חייב להיות קטן או שווה ל- :value קילובייט.',
        'string' => ':attribute חייב להיות קטן או שווה ל- :value תווים.',
        'array' => ':attribute לא יכול לכלול יותר מ- :value פריטים.',
    ],
    'max' => [
        'numeric' => ':attribute לא יכול להיות גדול מ- :max.',
        'file' => ':attribute לא יכול להיות גדול מ- :max קילובייט.',
        'string' => ':attribute לא יכול להיות גדול מ- :max תווים.',
        'array' => ':attribute לא יכול לכלול יותר מ- :max פריטים.',
    ],
    'mimes' => ':attribute חייב להיות קובץ מסוג: :values.',
    'mimetypes' => ':attribute חייב להיות קובץ מסוג: :values.',
    'min' => [
        'numeric' => ':attribute חייב להיות לפחות :min.',
        'file' => ':attribute חייב להיות לפחות :min קילובייט.',
        'string' => ':attribute חייב להיות לפחות :min תווים.',
        'array' => ':attribute חייב לכלול לפחות :min פריטים.',
    ],
    'multiple_of' => ':attribute חייב להיות כפולה של :value.',
    'not_in' => ':attribute הנבחר אינו חוקי.',
    'not_regex' => 'הפורמט של :attribute אינו חוקי.',
    'numeric' => ':attribute חייב להיות מספר.',
    'password' => [
        'letters' => ':attribute חייב להכיל לפחות אות אחת.',
        'mixed' => ':attribute חייב להכיל לפחות אות אחת גדולה ואות אחת קטנה.',
        'numbers' => ':attribute חייב להכיל לפחות מספר אחד.',
        'symbols' => ':attribute חייב להכיל לפחות סימן אחד.',
        'uncompromised' => ':attribute שניתן נמצא בהדלפה. נא לבחור סיסמה אחרת.',
    ],
    'present' => 'שדה :attribute חייב להיות נוכח.',
    'prohibited' => 'שדה :attribute אסור.',
    'prohibited_if' => 'שדה :attribute אסור כאשר :other הוא :value.',
    'prohibited_unless' => 'שדה :attribute אסור אלא אם :other הוא ב- :values.',
    'prohibits' => 'שדה :attribute מונע מ- :other להיות נוכח.',
    'regex' => 'הפורמט של :attribute אינו חוקי.',
    'required' => 'שדה :attribute נדרש.',
    'required_if' => 'שדה :attribute נדרש כאשר :other הוא :value.',
    'required_unless' => 'שדה :attribute נדרש אלא אם :other הוא ב- :values.',
    'required_with' => 'שדה :attribute נדרש כאשר :values נוכחים.',
    'required_with_all' => 'שדה :attribute נדרש כאשר כל :values נוכחים.',
    'required_without' => 'שדה :attribute נדרש כאשר :values אינם נוכחים.',
    'required_without_all' => 'שדה :attribute נדרש כאשר אף אחד מ- :values אינם נוכחים.',
    'same' => ':attribute ו- :other חייבים להיות תואמים.',
    'size' => [
        'numeric' => ':attribute חייב להיות :size.',
        'file' => ':attribute חייב להיות :size קילובייט.',
        'string' => ':attribute חייב להיות :size תווים.',
        'array' => ':attribute חייב לכלול :size פריטים.',
    ],
    'starts_with' => ':attribute חייב להתחיל באחד מהבאים: :values.',
    'string' => ':attribute חייב להיות מחרוזת.',
    'timezone' => ':attribute חייב להיות אזור זמן חוקי.',
    'unique' => ':attribute כבר נמצא בשימוש.',
    'uploaded' => 'העלאת :attribute נכשלה.',
    'url' => ':attribute חייב להיות URL חוקי.',
    'uuid' => ':attribute חייב להיות UUID חוקי.',
    /*
    |--------------------------------------------------------------------------
    | שורות אישור מותאמות אישית
    |--------------------------------------------------------------------------
    |
    | כאן אתם יכולים להגדיר הודעות שגיאה מותאמות אישית עבור תכונות תוך
    | שימוש בתבנית "attribute.rule". זה מאפשר להגדיר במהירות שורת הודעה
    | מותאמת אישית עבור כלל אימות ספציפי.
    |
    */
    'email_list' => 'מצטערים, דומיין זה של אימיילים אינו מורשה לשימוש באתר זה. אנא עיינו ברשימת הדומיינים המורשים.',
    'recaptcha' => 'נא להשלים את אימות ה-ReCaptcha.',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'הודעה מותאמת אישית',
        ],
    ],
];
