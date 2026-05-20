<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Animal;
<<<<<<< HEAD
use pocketmine\item\VanillaItems;
=======
>>>>>>> 018dd6d048cbb1352c9a33566660fd435ec30cfb

class Cat extends Animal {
    public static function getNetworkTypeId(): string {
        return "minecraft:cat";
    }

    public function isBreedingItem(\pocketmine\item\Item $item): bool {
<<<<<<< HEAD
        return $item->equals(VanillaItems::RAW_FISH(), true, false) || $item->equals(VanillaItems::RAW_SALMON(), true, false);
=======
        $typeId = $item->getTypeId();
        return $typeId === \pocketmine\item\VanillaItems::RAW_COD()->getTypeId() ||
               $typeId === \pocketmine\item\VanillaItems::RAW_SALMON()->getTypeId();
>>>>>>> 018dd6d048cbb1352c9a33566660fd435ec30cfb
    }
}
