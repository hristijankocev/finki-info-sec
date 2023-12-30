<?php

namespace service;

use Roles;

include 'model/enum/Roles.php';


function getRole(): string
{
    return $_SESSION['role'];
}

function isAdmin(): bool
{
    return getRole() === Roles::ADMINISTRATOR->value;
}

function isRegular(): bool
{
    return getRole() === Roles::REGULAR->value;
}

function isModerator(): bool
{
    return getRole() === Roles::MODERATOR->value;
}