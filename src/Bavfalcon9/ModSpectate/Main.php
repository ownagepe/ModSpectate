<?php

namespace Bavfalcon9\ModSpectate;

/* Commands */
use pocketmine\plugin\PluginBase;
use pocketmine\command\{
    Command,
    CommandSender
};
use pocketmine\permission\Permission;

/* Misc */
use pocketmine\{
    Player,
    Server
};

/* Commands */
use Bavfalcon9\ModSpectate\Command\{
    spectate,
    watch
};

/* Events */
use Bavfalcon9\ModSpectate\EventManager;

class Main extends PluginBase {
    public $EventManager;
    public $spectators = [];

    public function onEnable() {
        $this->EventManager = new EventManager($this);
        $this->getServer()->getPluginManager()->registerEvents($this->EventManager, $this);
        $this->loadCommands();
    }

    private function loadCommands() {
        $commandMap = $this->getServer()->getCommandMap();
        $commandMap->registerAll('spectate', [
            new spectate($this),
            new watch($this)
        ]);

        $this->addPerms([
            new Permission('spectate.command', 'Allows the player to spectate.', Permission::DEFAULT_OP),
            new Permission('spectate.commands', 'Allows the player to use commands while spectating.', Permission::DEFAULT_OP)
        ]);
    }

    /**
     * @param Permission[] $permissions
     */

    protected function addPerms(array $permissions) {
        foreach ($permissions as $permission) {
            $this->getServer()->getPluginManager()->addPermission($permission);
        }
    }

}