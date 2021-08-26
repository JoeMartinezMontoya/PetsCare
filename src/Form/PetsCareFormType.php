<?php

namespace App\Form;

use Symfony\Component\Security\Core\User\UserInterface;

class PetsCareFormType
{
    /**
     * Returns a formatted array
     * @param array $const
     * @return array
     */
    public function getChoices(array $const): array
    {
        $output = [];
        foreach ($const as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    /**
     * Returns a array containing the User pets names
     * @param UserInterface $user
     * @return array
     */
    public function getUserPetList(UserInterface $user): array
    {
        $list = [];
        foreach ($user->getPets() as $pet) {
            $list[] = $pet->getName();
        }
        return array_flip($list);
    }
}