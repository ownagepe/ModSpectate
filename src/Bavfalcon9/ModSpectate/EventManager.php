<?php

namespace Bavfalcon9\ModSpectate;

use Bavfalcon9\ModSpectate\Main;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\player\{
    PlayerCommandPreprocessEvent,
    PlayerQuitEvent,
    PlayerJoinEvent
};
use pocketmine\{
    Player,
    Server
};


class EventManager implements Listener {
    private $plugin;
    
    public function __construct(Main $pl) {
        $this->plugin = $pl;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if (isset($this->plugin->spectators[$player->getName()])) {
           $person = $this->plugin->spectators[$player->getName()]['player'];
           $location = $this->plugin->spectators[$player->getName()]['location'];
           $gamemode = $this->plugin->spectators[$player->getName()]['gm'];

           unset($this->plugin->spectators[$player->getName()]);

           $player->sendMessage(TF::GREEN . "You are no longer spectating $person.");
           $player->teleport($location);
           $player->setGamemode($gamemode);

           return true;
        } else {
            if ($player->getGamemode() !== 3) return false;

            $gamemode = Server::getInstance()->getDefaultGamemode();
            $location = $player->getLevel()->getSpawnLocation();
            $player->teleport($location);
            $player->setGamemode($gamemode);
            $player->sendMessage(TF::GREEN . "You were teleported to spawn because you were spectating.");

            return true;
        }
    }
    public function onCmd(PlayerCommandPreprocessEvent $event) {
        $player = $event->getPlayer();
        $str = str_split($event->getMessage());
        $command = explode(' ', $event->getMessage())[0];
        $commandCheck = false;
        $allowed = ['/freeze', '/thaw', '/unfreeze', '/ping', '/msg', '/pms'];
        
        if(strpos($event->getMessage(), '/watch') !== false) $commandCheck = true;
        if(strpos($event->getMessage(), '/spectate') !== false) $commandCheck = true;

        // Make sure they are spectating.
        if (!isset($this->plugin->spectators[$player->getName()])) return;
        // Make sure it's actually a command.
        if ($str[0] != '/') {
            return;
        }


        // Do the command check
        if (!$commandCheck) {
            if (!$player->hasPermission('spectate.commands') && !$player->isOp()) {
                if (in_array(strtolower($command), $allowed)) return;
                
                $player->sendMessage(TF::RED . 'You can not use commands while spectating.');
                return $event->setCancelled(true);
            }
        } else return;
    }
}