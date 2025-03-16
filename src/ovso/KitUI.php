<?php

namespace ovso;

use pocketmine\player\Player;
use pocketmine\form\Form;
use pocketmine\form\FormButton;
use pocketmine\form\FormIcon;
use pocketmine\form\MenuForm;

class KitUI {

    public static function sendKitSelection(Player $player, array $kits): void {
        $buttons = [];
        foreach ($kits as $kitName => $kit) {
            $buttons[] = new FormButton($kitName);
        }

        $form = new MenuForm(
             "Kit-Auswahl",
             "Wähle ein Kit aus:",
             $buttons,
             function (Player $player, int $selectedButton) use ($kits) {
                 $kitNames = array_keys($kits);
                 $selectedKit = $kits[$kitNames[$selectedButton]];
                 KitManager::applyKit($player, $selectedKit);
                 $player->sendMessage("Du hast das Kit '" . $kitNames[$selectedButton] . "' ausgewählt.");

                 $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
                 $queue = $plugin
            }
        );

        $player->sendForm($form);
    }
}
