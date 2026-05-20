<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Location;

abstract class BaseMob extends Living {
    protected int $tickOffset = 0;
    protected ?Vector3 $targetPosition = null;

    public function __construct(Location $location, ?CompoundTag $nbt = null) {
        $this->tickOffset = mt_rand(0, 20);
        parent::__construct($location, $nbt);
        if ($this->isFlying()) {
            $this->gravity = 0.0;
        }
    }

    protected function initEntity(CompoundTag $nbt): void {
        $this->setMaxHealth($this->getDefaultMaxHealth());
        parent::initEntity($nbt);

        if ($nbt->getTag("MaxHealth") !== null) {
            $this->setMaxHealth($nbt->getInt("MaxHealth"));
        }

        $healthTag = $nbt->getTag("Health");
        if ($healthTag !== null) {
            $this->setHealth(min($healthTag->getValue(), $this->getMaxHealth()));
        } else {
            $this->setHealth($this->getMaxHealth());
        }
    }

    public function saveNBT(): CompoundTag {
        $nbt = parent::saveNBT();
        $nbt->setInt("MaxHealth", $this->getMaxHealth());
        return $nbt;
    }

    public function getDefaultMaxHealth(): int {
        $className = static::class;
        $parts = explode('\\', $className);
        $name = strtolower(array_pop($parts));

        $healthMap = [
            'cow' => 10,
            'pig' => 10,
            'sheep' => 8,
            'chicken' => 4,
            'wolf' => 20,
            'cat' => 10,
            'ocelot' => 10,
            'horse' => 20,
            'donkey' => 20,
            'mule' => 20,
            'llama' => 20,
            'traderllama' => 20,
            'fox' => 10,
            'panda' => 20,
            'turtle' => 30,
            'dolphin' => 10,
            'squid' => 10,
            'glowsquid' => 10,
            'bat' => 6,
            'villager' => 20,
            'wanderingtrader' => 20,
            'irongolem' => 100,
            'snowgolem' => 4,
            'axolotl' => 14,
            'goat' => 20,
            'frog' => 10,
            'tadpole' => 6,
            'camel' => 32,
            'sniffer' => 14,
            'allay' => 20,
            'bee' => 10,
            'cod' => 3,
            'salmon' => 3,
            'pufferfish' => 3,
            'tropicalfish' => 3,
            'skeleton' => 20,
            'zombie' => 20,
            'zombievillager' => 20,
            'husk' => 20,
            'drowned' => 20,
            'stray' => 20,
            'creeper' => 20,
            'spider' => 16,
            'cavespider' => 12,
            'slime' => 16,
            'silverfish' => 8,
            'witch' => 26,
            'phantom' => 20,
            'vindicator' => 24,
            'evoker' => 24,
            'pillager' => 24,
            'ravager' => 100,
            'vex' => 14,
            'guardian' => 30,
            'elderguardian' => 80,
            'zombifiedpiglin' => 20,
            'piglin' => 16,
            'piglinbrute' => 50,
            'hoglin' => 40,
            'zoglin' => 40,
            'ghast' => 10,
            'blaze' => 20,
            'magmacube' => 16,
            'witherskeleton' => 20,
            'strider' => 20,
            'enderman' => 40,
            'endermite' => 8,
            'shulker' => 30,
            'enderdragon' => 200,
        ];

        return $healthMap[$name] ?? 20;
    }

    public function getAttackDamage(): float {
        $className = static::class;
        $parts = explode('\\', $className);
        $name = strtolower(array_pop($parts));

        $damageMap = [
            'zombie' => 3.0,
            'zombievillager' => 3.0,
            'husk' => 3.0,
            'drowned' => 3.0,
            'skeleton' => 2.0,
            'stray' => 2.0,
            'creeper' => 3.0,
            'spider' => 2.0,
            'cavespider' => 2.0,
            'silverfish' => 1.0,
            'witch' => 2.0,
            'phantom' => 6.0,
            'vindicator' => 8.0,
            'evoker' => 6.0,
            'pillager' => 4.0,
            'ravager' => 12.0,
            'vex' => 3.0,
            'guardian' => 6.0,
            'elderguardian' => 8.0,
            'zombifiedpiglin' => 5.0,
            'piglin' => 5.0,
            'piglinbrute' => 12.0,
            'hoglin' => 7.0,
            'zoglin' => 7.0,
            'ghast' => 6.0,
            'blaze' => 6.0,
            'magmacube' => 4.0,
            'witherskeleton' => 8.0,
            'strider' => 2.0,
            'enderman' => 7.0,
            'endermite' => 2.0,
            'shulker' => 4.0,
            'enderdragon' => 10.0,
            'irongolem' => 15.0,
            'wolf' => 4.0,
        ];

        return $damageMap[$name] ?? 3.0;
    }

    public function isFlying(): bool {
        return false;
    }

    public function isAquatic(): bool {
        return false;
    }

    public function isInsideOfWater(): bool {
        $block = $this->getWorld()->getBlock($this->location);
        if ($block instanceof \pocketmine\block\Water) {
            return true;
        }
        $eyeBlock = $this->getWorld()->getBlock($this->location->add(0, $this->getEyeHeight(), 0));
        return $eyeBlock instanceof \pocketmine\block\Water;
    }

    public function canBreathe(): bool {
        if ($this->isAquatic()) {
            return $this->isInsideOfWater() || $this->isUnderwater();
        }
        return parent::canBreathe();
    }

    public function isSwimming(): bool {
        return $this->isAquatic() && $this->isInsideOfWater();
    }

    public function getInitialGravity(): float {
        return $this->isFlying() ? 0.0 : parent::getInitialGravity();
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function isUndead(): bool {
        return false;
    }

    public function getName(): string {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

    public function onUpdate(int $currentTick): bool {

        if ($this->isSwimming()) {
            $this->gravity = 0.0;
        } else {
            $this->gravity = $this->isFlying() ? 0.0 : $this->getInitialGravity();
        }

        $hasUpdate = parent::onUpdate($currentTick);

        if (!$this->isAlive() || $this->isClosed()) {
            return $hasUpdate;
        }

        if (($currentTick + $this->tickOffset) % 10 === 0) {
            $this->calculateAI();
            $hasUpdate = true;

            if ($this->isUndead() && $this->getWorld()->getTimeOfDay() < \pocketmine\world\World::TIME_NIGHT) {
                if (!\BeeAZ\AZVanillaMobs\listener\EventListener::isWorldRaining($this->getWorld())) {
                    $x = $this->location->getFloorX();
                    $z = $this->location->getFloorZ();
                    if ($this->getWorld()->isChunkGenerated($x >> 4, $z >> 4) && $this->getWorld()->getHighestBlockAt($x, $z) <= $this->location->getFloorY()) {
                        $helmet = $this->getArmorInventory() !== null ? $this->getArmorInventory()->getHelmet() : null;
                        if ($helmet === null || $helmet->isNull()) {
                            $this->setOnFire(8);
                        }
                    }
                }
            }
        }

        if ($this->targetPosition !== null) {
            $this->moveTowardsTarget();
            $hasUpdate = true;
        }

        return $hasUpdate;
    }

    protected function calculateAI(): void {

    }

    protected function moveTowardsTarget(): void {
        if ($this->targetPosition === null) return;

        $x = $this->targetPosition->x - $this->location->x;
        $y = $this->targetPosition->y - $this->location->y;
        $z = $this->targetPosition->z - $this->location->z;

        if ($this->isFlying() || $this->isSwimming()) {
            if ($this->isCollidedHorizontally) {
                $this->targetPosition = null;
                $this->motion->x = 0;
                $this->motion->z = 0;
                return;
            }
            $distanceSq = $x * $x + $y * $y + $z * $z;
            if ($distanceSq < 1.5) {
                $this->targetPosition = null;
                $this->motion->x = 0;
                $this->motion->y = 0;
                $this->motion->z = 0;
                return;
            }

            $speed = $this->getMovementSpeed();
            $distance = sqrt($distanceSq);
            $motionX = ($x / $distance) * $speed;
            $motionY = ($y / $distance) * $speed;
            $motionZ = ($z / $distance) * $speed;

            $this->motion->x = $motionX;
            $this->motion->y = $motionY;
            $this->motion->z = $motionZ;

            $yaw = rad2deg(atan2($z, $x)) - 90;
            $pitch = -rad2deg(atan2($y, sqrt($x * $x + $z * $z)));
            $this->setRotation($yaw, $pitch);
        } else {
            $distanceSq = $x * $x + $z * $z;
            if ($distanceSq < 1.0) {
                $this->targetPosition = null;
                $this->motion->x = 0;
                $this->motion->z = 0;
                return;
            }

            $speed = $this->getMovementSpeed();
            $angle = atan2($z, $x);

            $motionX = cos($angle) * $speed;
            $motionZ = sin($angle) * $speed;

            if ($this->onGround && $this->targetPosition->y >= $this->location->y - 1) {
                $nextX = $this->location->x + $motionX * 2;
                $nextZ = $this->location->z + $motionZ * 2;
                $blockBelowNext = $this->getWorld()->getBlockAt((int)floor($nextX), (int)floor($this->location->y - 0.1), (int)floor($nextZ));
                if ($blockBelowNext->isTransparent() && $blockBelowNext->getTypeId() === \pocketmine\block\BlockTypeIds::AIR) {
                    $motionX = 0;
                    $motionZ = 0;
                    $this->targetPosition = null;
                }
            }

            $this->motion->x = $motionX;
            $this->motion->z = $motionZ;

            $this->setRotation(rad2deg($angle) - 90, 0);

            if ($this->onGround) {

                $nextX = $this->location->x + cos($angle) * 0.5;
                $nextZ = $this->location->z + sin($angle) * 0.5;

                $blockInFront = $this->getWorld()->getBlockAt((int)floor($nextX), (int)floor($this->location->y), (int)floor($nextZ));
                $blockAboveFront = $this->getWorld()->getBlockAt((int)floor($nextX), (int)floor($this->location->y + 1.0), (int)floor($nextZ));

                if ($blockInFront->isSolid() && !$blockAboveFront->isSolid()) {
                    $this->motion->y = 0.42;
                }
            }

            if ($this->isCollidedHorizontally && $this->onGround) {
                $this->motion->y = 0.42;
            }
        }
    }

    public function getMovementSpeed(): float {
        return 0.2;
    }

    public function getXpDropAmount(): int {
        return 0;
    }
}
