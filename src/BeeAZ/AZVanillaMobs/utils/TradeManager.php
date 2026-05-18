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

        return CompoundTag::create()
            ->setByte("Count", $item->getCount())
            ->setShort("Damage", $netItem->getMeta())
            ->setString("Name", $itemName);
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
                ->setInt("maxUses", $recipeData['maxUses'] ?? 10)
                ->setInt("uses", 0)
                ->setByte("rewardExp", 0)
                ->setInt("traderExp", 0)
                ->setInt("tier", 0)
                ->setFloat("priceMultiplierA", 0.0)
                ->setFloat("priceMultiplierB", 0.0)
                ->setInt("demand", 0)
                ->setInt("recipeNetworkId", $idx);

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
