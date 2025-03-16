<?php

namespace ovso;

use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\potion\EffectInstance;
use pocketmine\potion\Effect;

class KitManager {

    public static function applyKit(Player $player, array $kit): void {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getEffects()->clear();

        if (isset($kit["items"])) {
            foreach ($kit["items"] as $itemString) {
                $parts = explode(":", $itemString);
                $item = ItemFactory::getInstance()->get(ItemIdentifier::fromString(strtoupper($parts[0])), 0, (int) ($parts[1] ?? 1));
                $player->getInventory()->addItem($item);
            }
        }

        if (isset($kit["armor"])) {
            $armorSlots = ["helmet", "chestplate", "leggings", "boots"];
            for ($i = 0; $i < count($kit["armor"]); $i++) {
                $itemString = $kit["armor"][$i];
                $item = ItemFactory::getInstance()->get(ItemIdentifier::fromString(strtoupper(explode(":", $itemString)[0])), 0, 1);
                $player->getArmorInventory()->setItem($i, $item);
            }
        }

        if (isset($kit["effects"])) {
            foreach ($kit["effects"] as $effectString) {
                $parts = explode(":", $effectString);
                $effect = new EffectInstance(
                    Effect::getEffect((int) $parts[0]),
                    (int) ($parts[2] ?? 20),
                    (int) ($parts[1] ?? 1)
                );
                $player->getEffects()->add($effect);
            }
        }
    }
}
