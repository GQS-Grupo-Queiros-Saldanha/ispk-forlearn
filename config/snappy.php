<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        // 'binay' => '/usr/local/bin/wkhtmltopdf',
        'timeout' => false,
        'options' => array(
            'encoding' => 'utf-8'
        ),
        'env' => array(),
    ),
    'image' => array(
        'enabled' => false,
        'binary' => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltoimage-amd64'),
        'timeout' => false,
        'options' => array(
            'encoding' => 'utf-8'
        ),
        'env' => array(),
    ),


);
