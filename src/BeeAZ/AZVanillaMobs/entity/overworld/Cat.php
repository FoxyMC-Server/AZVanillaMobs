<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Animal;

class Cat extends Animal {
    public static function getNetworkTypeId(): string {
        return "minecraft:cat";
    }

    public function isBreedingItem(\pocketmine\item\Item $item): bool {
        $typeId = $item->getTypeId();
        return $typeId === \pocketmine\item\VanillaItems::RAW_COD()->getTypeId() ||
               $typeId === \pocketmine\item\VanillaItems::RAW_SALMON()->getTypeId();
    }
}
