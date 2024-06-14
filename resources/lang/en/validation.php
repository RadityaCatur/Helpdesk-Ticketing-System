<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus tanggal sesudah :date.',
    'after_or_equal' => ':attribute harus tanggal sesudah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung dan underscore.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus sebuah array.',
    'before' => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus diantara :min and :max.',
        'file' => ':attribute harus diantara :min and :max kilobytes.',
        'string' => ':attribute harus diantara :min and :max characters.',
        'array' => ':attribute harus diantara :min and :max items.',
    ],
    'boolean' => ':attribute harus true atau false.',
    'confirmed' => ':attribute konfirmasi tidak sesuai.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus sebuah tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus diantara :min dan :max digit.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai yang sama.',
    'email' => ':attribute harus memiliki alamat emaail yang valid.',
    'ends_with' => ':attribute harus selesai dengan: :values.',
    'exists' => 'Data yang dipilih :attribute tidak valid.',
    'file' => ':attribute harus file.',
    'filled' => ':attribute harus memiliki nilai.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file' => ':attribute harus lebih besar dari :value kilobytes.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
        'array' => ':attribute harus memiliki lebih dari :value item.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'file' => ':attribute harus lebih besar atau sama dengan :value kilobytes.',
        'string' => ':attribute harus lebih besar atau sama dengan :value karakter.',
        'array' => ':attribute harus memiliki :value item atau lebih.',
    ],
    'image' => ':attribute harus sebuah gambar.',
    'in' => 'Data yang dipilih :attribute tidak valid.',
    'in_array' => ':attribute tidak ada di :other.',
    'integer' => ':attribute harus bilangan bulat.',
    'ip' => ':attribute harus memiliki valid IP address.',
    'ipv4' => ':attribute harus memiliki valid IPv4 address.',
    'ipv6' => ':attribute harus memiliki valid IPv6 address.',
    'json' => ':attribute harus memiliki valid JSON string.',
    'lt' => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file' => ':attribute harus kurang dari :value kilobytes.',
        'string' => ':attribute harus kurang dari :value characters.',
        'array' => ':attribute harus kurang dari :value items.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang atau sama dengan :value.',
        'file' => ':attribute harus kurang atau sama dengan :value kilobytes.',
        'string' => ':attribute harus kurang atau sama dengan :value characters.',
        'array' => ':attribute tidak boleh lebih dari :value item.',
    ],
    'max' => [
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'file' => ':attribute tidak boleh lebih besar dari 2 mb.',
        'string' => ':attribute tidak boleh lebih besar dari :max characters.',
        'array' => ':attribute tidak boleh lebih dari :max item.',
    ],
    'mimes' => ':attribute harus bertipe: :values.',
    'mimetypes' => ':attribute harus bertipe: :values.',
    'min' => [
        'numeric' => ':attribute setidaknya harus :min.',
        'file' => ':attribute setidaknya harus :min kilobytes.',
        'string' => ':attribute setidaknya harus :min characters.',
        'array' => ':attribute setidaknya harus memiliki :min item.',
    ],
    'not_in' => 'Data yang dipilih :attribute tidak valid.',
    'not_regex' => ':attribute format tidak valid.',
    'numeric' => ':attribute harus angka.',
    'password' => 'Password yang dimasukan salah.',
    'present' => ':attribute harus ada.',
    'regex' => ':attribute harus huruf dan angka.',
    'required' => ':attribute harus diisi.',
    'required_if' => ':attribute harus diisi jika :other adalah :value.',
    'required_unless' => ':attribute harus diisi kecuali :other ada di :values.',
    'required_with' => ':attribute harus diisi jika :values ada.',
    'required_with_all' => ':attribute harus diisi jika :values saat ini.',
    'required_without' => ':attribute harus diisi jika :values tidak ada.',
    'required_without_all' => ':attribute harus diisi jika tidak ada :values saat ini.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobytes.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus mengandung :size item.',
    ],
    'starts_with' => ':attribute harus dimulai dengan: :values.',
    'string' => ':attribute input data harus string.',
    'timezone' => ':attribute harus zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal mengupload.',
    'url' => ':attribute format tidak valid.',
    'uuid' => ':attribute harus menggunakan UUID yang valid.',

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

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        
    ],

];
