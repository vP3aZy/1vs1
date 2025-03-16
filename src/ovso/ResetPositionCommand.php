<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class ResetPositionCommand extends Command {

    public function __construct() {
        parent::__construct("resetposition", "Setzt die Spawn-Positionen der Arena zurück", "/1vs1 resetposition");
        $this->setPermission("ovso.1vs1.resetposition");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $plugin = $sender->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        $config->set("arena1", [
            "x" => 0,
            "y" => 0,
            "z" => 0,
            "world" => "world"
        ]);

        $config->set("arena2", [
            "x" => 0,
            "y" => 0,
            "z" => 0,
            "world" => "world"
        ]);

        $config->save();

        $sender->sendMessage("Die Arena-Positionen wurden zurückgesetzt.");

        return true;
    }
}
