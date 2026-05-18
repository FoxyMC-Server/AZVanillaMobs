# 🐾 AZVanillaMobs

**A highly optimized vanilla mobs plugin for PocketMine-MP 5**

> Complete vanilla mob system featuring intelligent AI, natural spawning, loot tables, villager trading, taming, riding mechanics, and over 65 entities.

---

## 📋 General Information

| Property | Value |
|---|---|
| **Plugin Name** | AZVanillaMobs |
| **Version** | 1.0.0 |
| **Author** | BeeAZ |
| **API** | PocketMine-MP 5.0.0 |
| **License** | MIT |

---

## 🎮 Main Features

### ⚙️ Optimized AI System
- AI updates every 10 ticks instead of every tick for better performance
- Random `tickOffset` per entity to distribute CPU load evenly
- Lightweight pathfinding system with jumping, swimming, and cliff avoidance
- Separate handling for flying, aquatic, and ground mobs

### 🌍 Natural Spawning
- Mobs naturally spawn around players (10–24 block radius)
- Hostile mobs only spawn at night with light level ≤ 7
- Passive mobs spawn on grass during daytime
- Aquatic mobs spawn in water, Nether mobs spawn in the Nether
- Configurable global and per-world mob caps

### ⚔️ Combat System
- Monsters chase players within 16 blocks
- Ignores Creative and Spectator players
- Attack animations included
- Undead mobs burn in sunlight unless wearing helmets
- Random armor equipment for monsters

### 💀 Loot System
- Vanilla-accurate loot tables
- Cooked drops when mobs die in fire
- Rare drop chances supported
- XP drops for all mobs

### 🥚 Spawn Eggs
- Automatically registers spawn eggs into Creative Inventory
- Correct entity spawning for all eggs
- Overrides default PocketMine spawn eggs

---

## 🐛 Entity List (65+ Entities)

### 🌳 Overworld Hostile Mobs
Zombie, Skeleton, Creeper, Spider, Cave Spider, Slime, Witch, Husk, Drowned, Stray, Phantom, Zombie Villager, Vindicator, Evoker, Pillager, Ravager, Vex, Guardian, Elder Guardian, Silverfish.

### 🌳 Overworld Passive Mobs
Cow, Pig, Sheep, Chicken, Wolf, Cat, Ocelot, Horse, Donkey, Mule, Llama, Trader Llama, Fox, Panda, Turtle, Dolphin, Squid, Glow Squid, Bat, Villager, Wandering Trader, Iron Golem, Snow Golem, Axolotl, Goat, Frog, Tadpole, Camel, Sniffer, Allay, Bee.

### 🔥 Nether Mobs
Zombified Piglin, Piglin, Piglin Brute, Hoglin, Zoglin, Ghast, Blaze, Magma Cube, Wither Skeleton, Strider.

### 🟣 The End Mobs
Enderman, Endermite, Shulker, Ender Dragon.

### 💥 Projectiles
GhastFireball, BlazeFireball, WitchPotion.

---

## 🐴 Rideable Mobs

Supported rideable mobs:
- Horse
- Donkey
- Mule
- Pig
- Camel
- Panda

Features:
- WASD movement
- Jump support
- Sneak to dismount
- Horse armor support

---

## 🐺 Taming System

### Wolf
- Tame using Bones
- Sit/stand toggle
- Owner following system
- Collar dye support
- Attacks hostile targets

### Cat
- Tame using Raw Fish

---

## 🏪 Villager Trading

- Fully functional villager trading UI
- Randomized trade recipes
- Unlimited trading
- Piglin bartering system included

---

## 🏗️ Golem Creation

### Iron Golem
Create using:
- 4 Iron Blocks
- 1 Pumpkin or Carved Pumpkin

### Snow Golem
Create using:
- 2 Snow Blocks
- 1 Pumpkin or Carved Pumpkin

---

## 🔧 Commands

### `/summon <mob> [amount]`
Spawn mobs at your location.

### `/azkill <@e|mob>`
Remove plugin entities.

---

## ⚙️ Configuration

```yaml
worlds:
  "world": "overworld"
  "nether": "nether"
  "the_end": "the_end"

global-mob-cap: 200
per-world-mob-cap: 70
'
##🚀 Installation
Place the plugin into the plugins/ folder
Start the server
Edit plugin_data/AZVanillaMobs/config.yml
Restart the server
##❓ FAQ
Q: Why are mobs not spawning?

Check your world mappings and mob cap settings in config.yml.

Q: Can I disable natural spawning?

Set per-world-mob-cap: 0 or remove the world from the config.

Q: Does /azkill affect other plugins?

No. It only removes entities from BeeAZ\AZVanillaMobs.

Made with ❤️ by BeeAZ
