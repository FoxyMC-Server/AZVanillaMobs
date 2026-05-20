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

    public function isFlying(): bool {
        return false;
    }

    public function isAquatic(): bool {
        return false;
    }

    public function isInsideOfWater(): bool {
        $block = $this->getWorld()->getBlock($this->location);
        return $block instanceof \pocketmine\block\Water;
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
<<<<<<< HEAD
                $x = $this->location->getFloorX();
                $z = $this->location->getFloorZ();
                if ($this->getWorld()->isChunkGenerated($x >> 4, $z >> 4) && $this->getWorld()->getHighestBlockAt($x, $z) <= $this->location->getFloorY()) {
=======
                if ($this->getWorld()->getHighestBlockAt($this->location->getFloorX(), $this->location->getFloorZ()) <= $this->location->getFloorY()) {
>>>>>>> 018dd6d048cbb1352c9a33566660fd435ec30cfb
                    $helmet = $this->getArmorInventory() !== null ? $this->getArmorInventory()->getHelmet() : null;
                    if ($helmet === null || $helmet->isNull()) {
                        $this->setOnFire(8);
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
