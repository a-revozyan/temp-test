<?php
return [
    'adminEmail' => 'admin@example.com',
    'bsVersion' => '4.x',
    'jwt' => [
        'issuer' => 'https://api.sugurtabozor.uz',  //name of your project (for information only)
        'audience' => 'https://gross.uz',  //description of the audience, eg. the website using the authentication (for info only)
        'id' => 'ln7FJVbeumVix7sB9saak2zztVwTK6NE1AlY5ch63r8dYkZaL8',  //a unique identifier for the JWT, typically a random string
        'expire' => 'PT0H5M0S',  //the short-lived JWT token is here set to expire after 5 min.
    ],
];
