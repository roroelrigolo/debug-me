<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\LevelRepository;
use App\Repository\UserRepository;

class Point
{
    public function __construct(
        private UserRepository $userRepository,
        private LevelRepository $levelRepository,
    ) {
    }
    //This function adds an extra point to the user and changes its level if necessary
    public function addPoint(User $user): string
    {
        $userRepository = $this->userRepository;
        $levelRepository = $this->levelRepository;
        $user->setPoints($user->getPoints()+1);

        $pts = $user->getPoints();
        if($pts > $user->getLevel()->getStage()){
            $nextLevel = $levelRepository->findOneBy(['step'=>$user->getLevel()->getStep()+1]);
            $user->setLevel($nextLevel);
        }

        $userRepository->add($user);
        return true;
    }
}