<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\utils;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class TradeManager {

    public static function getItemNbt(Item $item): CompoundTag {
        $netItem = TypeConverter::getInstance()->coreItemStackToNet($item);
        $itemName = GlobalItemDataHandlers::getSerializer()->serializeType($item)->getName();

<<<<<<< HEAD
        $tag = CompoundTag::create()
            ->setByte("Count", $item->getCount())
            ->setShort("Damage", $netItem->getMeta())
            ->setString("Name", $itemName);

        if ($item->hasNamedTag()) {
            $tag->setTag("tag", clone $item->getNamedTag());
        }

        return $tag;
=======
        return CompoundTag::create()
            ->setByte("Count", $item->getCount())
            ->setShort("Damage", $netItem->getMeta())
            ->setString("Name", $itemName);
>>>>>>> 018dd6d048cbb1352c9a33566660fd435ec30cfb
    }

    public static function buildOffersNbt(array $recipes): CacheableNbt {
        $recipeList = new ListTag();

        foreach ($recipes as $idx => $recipeData) {
            $buyA = $recipeData['buyA'];
            $buyB = $recipeData['buyB'] ?? null;
            $sell = $recipeData['sell'];

            $recipeTag = CompoundTag::create()
                ->setTag("buyA", self::getItemNbt($buyA))
                ->setInt("buyCountA", $buyA->getCount())
                ->setTag("sell", self::getItemNbt($sell))
<<<<<<< HEAD
                ->setInt("maxUses", $recipeData['maxUses'] ?? 12)
                ->setInt("uses", $recipeData['uses'] ?? 0)
                ->setByte("rewardExp", (int)($recipeData['rewardExp'] ?? 1))
                ->setInt("traderExp", $recipeData['traderExp'] ?? 2)
                ->setInt("tier", ($recipeData['tier'] ?? 1) - 1) // Client tier index is 0-indexed (Novice is 0)
                ->setFloat("priceMultiplierA", $recipeData['priceMultiplierA'] ?? 0.05)
                ->setFloat("priceMultiplierB", $recipeData['priceMultiplierB'] ?? 0.05)
                ->setInt("demand", $recipeData['demand'] ?? 0)
                ->setInt("recipeNetworkId", 32000 + $idx);
=======
                ->setInt("maxUses", $recipeData['maxUses'] ?? 10)
                ->setInt("uses", 0)
                ->setByte("rewardExp", 0)
                ->setInt("traderExp", 0)
                ->setInt("tier", 0)
                ->setFloat("priceMultiplierA", 0.0)
                ->setFloat("priceMultiplierB", 0.0)
                ->setInt("demand", 0)
                ->setInt("recipeNetworkId", $idx);
>>>>>>> 018dd6d048cbb1352c9a33566660fd435ec30cfb

            if ($buyB !== null && !$buyB->isNull()) {
                $recipeTag->setTag("buyB", self::getItemNbt($buyB));
                $recipeTag->setInt("buyCountB", $buyB->getCount());
            }

            $recipeList->push($recipeTag);
        }

        $offers = CompoundTag::create()
            ->setTag("Recipes", $recipeList);

        return new CacheableNbt($offers);
    }
}
