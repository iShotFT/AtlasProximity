<?php

return [
    'meta'      => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'       => 'Atlas Discord Server',
            // set false to total remove
            'description' => 'An automated way to track players, alert when boats enter your coordinate and much more for the official servers of the the pirate game Atlas.',
            // set false to total remove
            'separator'   => ' - ',
            'keywords'    => [
                'iShot',
                'Discord',
                'Atlas',
                'Bot',
                'automatic',
                'install',
            ],
            'canonical'   => null,
            // Set null for using Url::current(), set false to total remove
        ],

        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => 'F9Mzxt7qvKWGsiht7QgIQU-wbJC7TEbCrpPi7QS3jfo',
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
        ],
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => 'Atlas Discord Server',
            // set false to total remove
            'description' => 'An automated way to track players, alert when boats enter your coordinate and much more for the official servers of the the pirate game Atlas.',
            // set false to total remove
            'url'         => null,
            // Set null for using Url::current(), set false to total remove
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],
    'twitter'   => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            //'card'        => 'summary',
            //'site'        => '@LuizVinicius73',
        ],
    ],
];
