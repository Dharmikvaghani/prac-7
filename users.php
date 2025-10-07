<?php
$USERS = [
    "admin" => [
        // password = admin123
        "password_hash" => password_hash("admin123", PASSWORD_DEFAULT)
    ],
    "student" => [
        // password = student123
        "password_hash" => password_hash("student123", PASSWORD_DEFAULT)
    ]
];