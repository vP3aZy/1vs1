<?php

namespace ovso;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\utils\Config;

class ovso extends PluginBase {

    private Config $config;
    public array $queue = []; // Warteschlange
    private array $challenges = []; // Herausforderungsanfragen

    public function onEnable(): void {
        $this->getLogger()->info("ovso Plugin aktiviert!");
        $mainCommand = new OneVsOneCommand();
        $this->getServer()->getCommandMap()->register("ovso", $mainCommand);
        $mainCommand->registerSubCommand(new CreateArenaCommand());
        $mainCommand->registerSubCommand(new SetPositionCommand());
        $mainCommand->registerSubCommand(new ResetPositionCommand());
        $mainCommand->registerSubCommand(new SetSpawnCommand());
        $this->getServer()->getCommandMap()->register("ovso", new StartCommand());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function getConfig(): Config {
       return $this->config;
   }

    public function onDisable(): void {
        $this->getLogger()->info("1vs1 Plugin deaktiviert!");
    }
}
