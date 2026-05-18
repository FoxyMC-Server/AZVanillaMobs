# 🐾 AZVanillaMobs

**Plugin Vanilla Mobs tối ưu hiệu suất tuyệt đối cho PocketMine-MP 5**

> Hệ thống mob vanilla hoàn chỉnh với AI thông minh, sinh sản tự nhiên, hệ thống loot, giao dịch dân làng, thuần hóa, cưỡi, và hơn 65 loại entity.

---

## 📋 Thông Tin Chung

| Thuộc tính | Giá trị |
|---|---|
| **Tên Plugin** | AZVanillaMobs |
| **Phiên bản** | 1.0.0 |
| **Tác giả** | BeeAZ |
| **API** | PocketMine-MP 5.0.0 |
| **Giấy phép** | Độc quyền |

---

## 🎮 Tính Năng Chính

### ⚙️ Hệ Thống AI Tối Ưu
- AI được tính toán mỗi 10 tick thay vì mỗi tick, giảm tải server đáng kể
- Mỗi entity có `tickOffset` ngẫu nhiên để phân tán tải CPU đều giữa các tick
- Hệ thống pathfinding nhẹ: mob tự nhảy qua block, tránh rơi vực, bơi trong nước
- Phân biệt rõ ràng giữa mob bay (gravity = 0), mob nước, và mob mặt đất

### 🌍 Sinh Sản Tự Nhiên (Natural Spawning)
- Mob tự sinh sản quanh người chơi (bán kính 10-24 block)
- Phân biệt thời gian ngày/đêm: mob thù địch chỉ spawn ban đêm khi ánh sáng ≤ 7
- Mob thụ động spawn trên cỏ vào ban ngày
- Mob nước spawn trong nước, mob nether spawn ở nether
- Giới hạn mob toàn cục và theo từng world có thể cấu hình

### ⚔️ Hệ Thống Chiến Đấu
- Monster tự truy đuổi người chơi trong phạm vi 16 block
- Bỏ qua người chơi ở chế độ Creative/Spectator
- Animation đánh tay khi tấn công
- Mob undead (Zombie, Skeleton, ...) tự cháy dưới ánh nắng nếu không đội mũ
- Monster có xác suất mặc giáp ngẫu nhiên (Leather/Iron/Gold)

### 💀 Hệ Thống Loot
- Mỗi loại mob có bảng loot riêng theo đúng vanilla
- Hỗ trợ loot nấu chín khi mob chết trong lửa (raw → cooked)
- Drop hiếm với tỉ lệ chính xác (VD: Wither Skeleton Skull 2.5%)
- XP drop theo từng loại mob

### 🥚 Spawn Egg
- Tự động đăng ký spawn egg cho tất cả mob vào Creative Inventory
- Spawn egg hoạt động chính xác, tạo đúng loại entity tương ứng
- Ghi đè spawn egg mặc định của PocketMine

---

## 🐛 Danh Sách Entity (65+ loại)

### 🌳 Overworld - Hostile (20 loại)

| Entity | Network ID | Tính Năng Đặc Biệt |
|---|---|---|
| **Zombie** | `minecraft:zombie` | Undead, cháy nắng, truy đuổi người chơi |
| **Skeleton** | `minecraft:skeleton` | Bắn cung từ xa (15 block), undead |
| **Creeper** | `minecraft:creeper` | Tự nổ khi đến gần, hiệu ứng phát sáng + phồng lên |
| **Spider** | `minecraft:spider` | Leo tường, trung lập ban ngày, thù địch ban đêm |
| **Cave Spider** | `minecraft:cave_spider` | Như Spider |
| **Slime** | `minecraft:slime` | Chia nhỏ khi chết (Large→Small→Tiny) |
| **Witch** | `minecraft:witch` | Ném bình thuốc độc vào người chơi |
| **Husk** | `minecraft:husk` | Biến thể Zombie sa mạc |
| **Drowned** | `minecraft:drowned` | Biến thể Zombie dưới nước |
| **Stray** | `minecraft:stray` | Biến thể Skeleton băng giá |
| **Phantom** | `minecraft:phantom` | Bay, tấn công từ trên cao |
| **Zombie Villager** | `minecraft:zombie_villager` | Biến thể Zombie dân làng |
| **Vindicator** | `minecraft:vindicator` | Cầm rìu sắt, tấn công mạnh |
| **Evoker** | `minecraft:evoker` | Drop Totem of Undying |
| **Pillager** | `minecraft:pillager` | Drop cung nỏ |
| **Ravager** | `minecraft:ravager` | Drop yên ngựa |
| **Vex** | `minecraft:vex` | Bay, xuyên tường |
| **Guardian** | `minecraft:guardian` | Mob nước, drop Prismarine |
| **Elder Guardian** | `minecraft:elder_guardian` | Mob nước, drop Wet Sponge |
| **Silverfish** | `minecraft:silverfish` | Mob nhỏ, ẩn trong đá |

### 🌳 Overworld - Passive (31 loại)

| Entity | Network ID | Tính Năng Đặc Biệt |
|---|---|---|
| **Cow** | `minecraft:cow` | Cho sữa, sinh sản bằng Wheat |
| **Pig** | `minecraft:pig` | Có thể cưỡi với Saddle, điều khiển bằng Carrot on a Stick |
| **Sheep** | `minecraft:sheep` | Cắt lông bằng Shears, nhuộm màu bằng Dye, mọc lại lông |
| **Chicken** | `minecraft:chicken` | Đẻ trứng mỗi 6000-12000 tick |
| **Wolf** | `minecraft:wolf` | Thuần hóa bằng Bone, ngồi/đứng, theo chủ, tấn công kẻ thù, nhuộm vòng cổ |
| **Cat** | `minecraft:cat` | Thuần hóa bằng Raw Fish |
| **Ocelot** | `minecraft:ocelot` | Chạy trốn khi đến gần |
| **Horse** | `minecraft:horse` | Cưỡi, nhảy cao, điều khiển WASD, mặc giáp ngựa |
| **Donkey** | `minecraft:donkey` | Cưỡi, điều khiển WASD |
| **Mule** | `minecraft:mule` | Cưỡi, điều khiển WASD |
| **Llama** | `minecraft:llama` | Mang thảm trang trí |
| **Trader Llama** | `minecraft:trader_llama` | Llama của Wandering Trader |
| **Fox** | `minecraft:fox` | Nhặt item trên mặt đất ngậm trong miệng, người chơi có thể lấy lại |
| **Panda** | `minecraft:panda` | Cưỡi được, AI đặc biệt |
| **Turtle** | `minecraft:turtle` | Mob nước |
| **Dolphin** | `minecraft:dolphin` | Mob nước |
| **Squid** | `minecraft:squid` | Mob nước, drop Ink Sac |
| **Glow Squid** | `minecraft:glow_squid` | Mob nước, drop Glow Ink Sac |
| **Bat** | `minecraft:bat` | Bay |
| **Villager** | `minecraft:villager_v2` | Hệ thống giao dịch hoàn chỉnh |
| **Wandering Trader** | `minecraft:wandering_trader` | Thương nhân lang thang |
| **Iron Golem** | `minecraft:iron_golem` | Bảo vệ dân làng, tấn công mob thù địch, tạo bằng Iron Block + Pumpkin |
| **Snow Golem** | `minecraft:snow_golem` | Tạo bằng Snow Block + Pumpkin |
| **Axolotl** | `minecraft:axolotl` | Mob nước, biến thể màu ngẫu nhiên |
| **Goat** | `minecraft:goat` | Drop thịt cừu |
| **Frog** | `minecraft:frog` | Biến thể màu ngẫu nhiên |
| **Tadpole** | `minecraft:tadpole` | Mob nước |
| **Camel** | `minecraft:camel` | Cưỡi được, AI đặc biệt |
| **Sniffer** | `minecraft:sniffer` | Drop Moss Block |
| **Allay** | `minecraft:allay` | Bay |
| **Bee** | `minecraft:bee` | Bay, nổi giận khi bị đánh, đốt một lần rồi chết |

### 🔥 Nether (10 loại)

| Entity | Network ID | Tính Năng Đặc Biệt |
|---|---|---|
| **Zombified Piglin** | `minecraft:zombie_pigman` | Trung lập, nổi giận cả bầy khi bị đánh |
| **Piglin** | `minecraft:piglin` | Đổi Gold Ingot lấy item ngẫu nhiên (barter), thù địch nếu không mặc giáp vàng |
| **Piglin Brute** | `minecraft:piglin_brute` | Luôn thù địch |
| **Hoglin** | `minecraft:hoglin` | Thù địch, drop thịt heo |
| **Zoglin** | `minecraft:zoglin` | Thù địch |
| **Ghast** | `minecraft:ghast` | Bay, bắn cầu lửa nổ từ xa |
| **Blaze** | `minecraft:blaze` | Bay, bắn 3 cầu lửa liên tiếp |
| **Magma Cube** | `minecraft:magma_cube` | Chia nhỏ khi chết (như Slime) |
| **Wither Skeleton** | `minecraft:wither_skeleton` | Drop Wither Skeleton Skull (2.5%) |
| **Strider** | `minecraft:strider` | Mob nether |

### 🟣 The End (4 loại)

| Entity | Network ID | Tính Năng Đặc Biệt |
|---|---|---|
| **Enderman** | `minecraft:enderman` | Dịch chuyển ngẫu nhiên, nhặt/đặt block, sợ nước, miễn nhiễm projectile, spawn ở cả 3 dimension |
| **Endermite** | `minecraft:endermite` | Mob nhỏ |
| **Shulker** | `minecraft:shulker` | Drop Shulker Shell |
| **Ender Dragon** | `minecraft:ender_dragon` | Bay, boss entity |

### 💥 Projectile (3 loại)

| Entity | Mô Tả |
|---|---|
| **GhastFireball** | Cầu lửa của Ghast, gây nổ |
| **BlazeFireball** | Cầu lửa nhỏ của Blaze, gây cháy |
| **WitchPotion** | Bình thuốc ném của Witch, gây Slowness + Poison |

---

## 🐴 Hệ Thống Cưỡi (Rideable Mobs)

Plugin hỗ trợ cưỡi đầy đủ cho các mob sau:

| Mob | Cách Cưỡi | Điều Khiển |
|---|---|---|
| **Horse** | Click chuột phải vào Horse | WASD + Nhìn, nhảy bằng Space |
| **Donkey** | Click chuột phải vào Donkey | WASD + Nhìn, nhảy bằng Space |
| **Mule** | Click chuột phải vào Mule | WASD + Nhìn, nhảy bằng Space |
| **Pig** | Cần Saddle, điều khiển bằng Carrot on a Stick | Hướng nhìn |
| **Camel** | Click chuột phải vào Camel | WASD + Nhìn |
| **Panda** | Click chuột phải vào Panda | WASD + Nhìn |

- Xuống: Sneak (Shift)
- Horse hỗ trợ mặc giáp ngựa (Iron/Gold/Diamond Horse Armor)

---

## 🐺 Hệ Thống Thuần Hóa

### Wolf
1. Cầm **Bone** và click chuột phải vào Wolf
2. Xác suất thuần hóa thành công: **33%** mỗi lần
3. Wolf đã thuần hóa:
   - Hiển thị trái tim khi thuần hóa thành công
   - Click chuột phải (tay không): **Ngồi / Đứng dậy**
   - Tự động theo chủ khi đứng (dịch chuyển nếu quá xa)
   - Tấn công mob mà chủ đánh
   - Nhuộm vòng cổ bằng **Dye** bất kỳ
   - Cho ăn thịt để hồi máu

### Cat
- Thuần hóa bằng **Raw Fish** (Raw Cod / Raw Salmon)

---

## 🏪 Hệ Thống Giao Dịch (Villager Trading)

### Cách Sử Dụng
1. Click chuột phải vào **Villager** để mở giao diện giao dịch
2. Chọn công thức giao dịch bất kỳ → item được trao đổi **tức thì**
3. Không cần bấm nút Trade, chỉ cần click chọn công thức

### Danh Sách Công Thức Giao Dịch

| Input A | Input B | Output |
|---|---|---|
| 5 Emerald | 32 Wheat | 64 Bread |
| 10 Emerald | 32 Carrot | 16 Golden Carrot |
| 15 Emerald | 10 Iron Ingot | 1 Iron Sword |
| 24 Emerald | 3 Diamond | 1 Diamond Pickaxe |
| 8 Emerald | 1 Book | 1 Enchanted Book |
| 12 Emerald | 64 Melon | 8 Glistering Melon |
| 6 Emerald | 32 Potato | 64 Baked Potato |
| 18 Emerald | 8 Gold Ingot | 4 Golden Apple |

- Mỗi Villager có **5-7 công thức** ngẫu nhiên từ bảng trên
- Không có hệ thống tier/cấp độ, tất cả công thức mở sẵn
- Không giới hạn số lần giao dịch

### Piglin Bartering
- Ném **Gold Ingot** (hoặc click chuột phải) vào Piglin
- Piglin sẽ nhặt vàng, chờ 3 giây, rồi ném lại item ngẫu nhiên
- Danh sách item có thể nhận: Obsidian, Ender Pearl, Iron Nugget, String, Fire Charge, Gravel, Leather, Nether Brick, Soul Sand, Nether Quartz, ...

---

## 🏗️ Hệ Thống Tạo Golem

### Iron Golem
Xếp cấu trúc sau:
```
     [Pumpkin]
  [Iron][Iron][Iron]
       [Iron]
```
- 4 Iron Block hình chữ T + 1 Pumpkin/Carved Pumpkin ở đầu
- Iron Golem tự động spawn khi đặt block cuối cùng

### Snow Golem
Xếp cấu trúc sau:
```
  [Pumpkin]
   [Snow]
   [Snow]
```
- 2 Snow Block xếp dọc + 1 Pumpkin/Carved Pumpkin ở đầu

---

## 🔧 Lệnh (Commands)

### `/summon <mob> [số_lượng]`
Triệu hồi mob tại vị trí người chơi.

| Tham số | Mô tả |
|---|---|
| `mob` | Tên mob (VD: `zombie`, `skeleton`, `wolf`) |
| `số_lượng` | Số lượng mob cần spawn (mặc định: 1) |

**Quyền:** `azvanillamobs.command.summon` (mặc định: OP)

**Ví dụ:**
```
/summon zombie 10
/summon wolf
/summon ghast 3
```

- Mob bay sẽ spawn cao hơn 2 block
- Mob nước sẽ tự tìm nguồn nước gần nhất để spawn

### `/azkill <@e|tên>`
Xóa mob của plugin.

| Tham số | Mô tả |
|---|---|
| `@e` | Xóa **tất cả** entity của AZVanillaMobs (bao gồm cả projectile) |
| `tên` | Xóa entity theo tên (VD: `zombie`, `skeleton`) |

**Quyền:** `azvanillamobs.command.kill` (mặc định: OP)

**Ví dụ:**
```
/azkill @e
/azkill zombie
/azkill ghast
```

> ⚠️ Lệnh chỉ xóa mob thuộc plugin AZVanillaMobs, không ảnh hưởng entity của plugin khác hay người chơi.

---

## ⚙️ Cấu Hình (config.yml)

```yaml
# Ánh xạ tên thư mục world → loại dimension
worlds:
  "skyblock": "overworld"
  "nether": "nether"
  "the_end": "the_end"

# Giới hạn mob toàn server
global-mob-cap: 200

# Giới hạn mob mỗi world
per-world-mob-cap: 70
```

### Giải Thích Config

| Thuộc tính | Mô tả | Mặc định |
|---|---|---|
| `worlds` | Map tên thư mục world → loại dimension (`overworld`, `nether`, `the_end`) | `world: overworld` |
| `global-mob-cap` | Tổng số entity tối đa trên toàn server | `200` |
| `per-world-mob-cap` | Số entity tối đa trong mỗi world | `70` |

---

## 📁 Cấu Trúc Thư Mục

```
AZVanillaMobs/
├── plugin.yml
├── README.md
├── resources/
│   └── config.yml
└── src/BeeAZ/AZVanillaMobs/
    ├── Main.php                    # Entry point, đăng ký entity & spawn egg
    ├── command/
    │   ├── SummonCommand.php       # Lệnh /summon
    │   └── KillCommand.php         # Lệnh /azkill
    ├── entity/
    │   ├── BaseMob.php             # Base class cho tất cả mob (AI, pathfinding)
    │   ├── Animal.php              # Base class cho mob thụ động (breeding, panic)
    │   ├── Monster.php             # Base class cho mob thù địch (chase, attack)
    │   ├── overworld/              # 51 entity overworld
    │   ├── nether/                 # 10 entity nether
    │   ├── the_end/                # 4 entity the end
    │   └── projectile/             # 3 projectile (Fireball, Potion)
    ├── listener/
    │   ├── EventListener.php       # Xử lý sự kiện (cưỡi, golem, spawn egg)
    │   └── TradeListener.php       # Xử lý giao dịch Villager
    ├── loot/
    │   └── LootManager.php         # Bảng loot drop cho tất cả mob
    ├── spawner/
    │   └── SpawnerTask.php         # Task sinh sản mob tự nhiên
    └── utils/
        └── TradeManager.php        # Xây dựng NBT giao dịch cho Villager
```

---

## 🧬 Kiến Trúc Entity

```
Living (PocketMine)
└── BaseMob
    ├── Animal (mob thụ động)
    │   ├── Cow, Pig, Sheep, Chicken, ...
    │   ├── Wolf (thuần hóa, vòng cổ, theo chủ)
    │   ├── Fox (nhặt item)
    │   ├── Horse/Donkey/Mule (cưỡi)
    │   ├── Villager (giao dịch)
    │   └── Bee (nổi giận, đốt)
    └── Monster (mob thù địch)
        ├── Zombie, Skeleton, Creeper, ...
        ├── Slime/MagmaCube (chia nhỏ)
        ├── Witch (ném thuốc)
        ├── Blaze/Ghast (bắn cầu lửa)
        ├── Piglin (barter)
        ├── Enderman (dịch chuyển, nhặt block)
        └── Spider (trung lập ban ngày)
```

---

## 🎯 Tính Năng Đặc Biệt Nổi Bật

### 🐑 Sheep - Cắt & Nhuộm Lông
- **Cắt lông:** Dùng Shears click chuột phải → drop 1-3 Wool
- **Nhuộm màu:** Dùng Dye click chuột phải → thay đổi màu lông
- **Mọc lại:** Lông tự mọc lại sau khi cắt (khi ăn cỏ)

### 💣 Creeper - Nổ
- Phát hiện người chơi trong 3 block → bắt đầu đếm ngược (30 tick)
- Phồng lên dần (scale tăng) + phát sáng
- Nổ tạo explosion gây sát thương

### 🏹 Skeleton - Bắn Cung
- Phát hiện người chơi trong 15 block
- Bắn Arrow với độ chính xác cao
- Cooldown 40 tick giữa các phát bắn

### 🕷️ Spider - Trung Lập Ban Ngày
- Ban ngày: Đi lang thang, không tấn công
- Ban đêm: Trở nên thù địch, truy đuổi người chơi
- Leo tường khi va chạm

### 🟢 Slime & Magma Cube - Chia Nhỏ
- Chết → spawn 2-4 con nhỏ hơn
- Large (size 4) → Small (size 2) → Tiny (size 1)
- Tiny không drop loot, chỉ Large và Small mới drop

### 👁️ Enderman
- Dịch chuyển ngẫu nhiên mỗi 80-400 tick
- Nhặt block tự nhiên (Grass, Dirt, Sand, Flower, ...)
- Đặt lại block đã nhặt
- Sợ nước (nhận sát thương khi đứng trong nước)
- Miễn nhiễm mọi projectile (tự dịch chuyển khi bị bắn)
- Dịch chuyển đến gần mục tiêu khi truy đuổi

### 🐝 Bee
- Nổi giận khi bị đánh → truy đuổi và đốt
- Đốt gây 2 sát thương + Poison 10 giây
- Chết sau khi đốt (như vanilla)
- Bình tĩnh lại sau 600 tick nếu không đốt được

### 🦊 Fox
- Tìm và nhặt item rơi trên mặt đất
- Ngậm item trong miệng (hiển thị trên model)
- Người chơi click tay không → lấy lại item
- Thả item khi chết

---

## 📝 Ghi Chú Kỹ Thuật

- Plugin ghi đè lệnh `/summon` mặc định của PocketMine
- Zombie mặc định của PocketMine bị thay thế bằng AZVanillaMobs Zombie
- Tất cả entity đều lưu/tải NBT đúng cách (persist qua restart)
- Saddle được đăng ký như custom item vào Creative Inventory
- Hệ thống giao dịch sử dụng `UpdateTradePacket` + `ItemStackRequestPacket` của Bedrock Protocol
- Trade UI chặn packet cẩn thận để không ảnh hưởng thao tác inventory thông thường

---

## 🚀 Cài Đặt

1. Tải file plugin và đặt vào thư mục `plugins/`
2. Khởi động server
3. Chỉnh sửa `plugin_data/AZVanillaMobs/config.yml` theo nhu cầu
4. Restart server

---

## ❓ FAQ

**Q: Mob không spawn?**
A: Kiểm tra config.yml đã map đúng tên thư mục world chưa. Đảm bảo mob cap chưa đạt giới hạn.

**Q: Spawn egg không hiện trong Creative?**
A: Plugin tự động thêm spawn egg. Nếu không thấy, thử restart server.

**Q: Có thể tắt spawn tự nhiên không?**
A: Xóa world khỏi config `worlds` hoặc đặt `per-world-mob-cap: 0`.

**Q: Lệnh /azkill có xóa entity plugin khác không?**
A: Không. Lệnh chỉ xóa entity thuộc namespace `BeeAZ\AZVanillaMobs`.

---

*Made with ❤️ by BeeAZ*
