<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\SuccesRepository;
use App\Repository\UserRepository;

class Succes
{
    public function __construct(
        private UserRepository $userRepository,
        private SuccesRepository $succesRepository,
    ) {
    }
    public function refreshSuccess(User $user): string
    {

    }



}