<?php

return [
    'css' => [
        'submit' => 'btn btn-danger btn-rounded btn-custom btn-block waves-effect waves-light'
    ],
    'generate' => [
        // string is for the validator, text is for the input
        'string' => [
            'validation' => 'string',
            'type'       => 'text',
            'class'      => 'form-control',
        ],
        'bigint' => [
            'validation' => 'integer',
            'type'       => 'select',
            'class'      => 'form-control',
        ],
        'date' => [
            'validation' => 'date_format:Y-m-d',
            'type'       => 'date',
            'class'      => 'form-control',
        ],
        'email' => [
            'validation' => '',
            'type'       => 'email',
            'class'      => 'form-control',
        ],
        'password' => [
            'validation' => '',
            'type'       => 'password',
            'class'      => 'form-control',
        ]
    ]
];