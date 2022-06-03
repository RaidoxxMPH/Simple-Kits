<?php

namespace Raidoxx;

use muqsit\invmenu\InvMenuHandler;

use Raidoxx\commands\CommandConfigKits;
use Raidoxx\commands\CommandKits;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;


class Main extends PluginBase implements Listener
{
    public function onEnable():void
    {
        $this->getLogger()->info("§eSIMPLE KITS v2");

        $this->getServer()->getCommandMap()->register("CommandKits", new CommandKits($this));
        $this->getServer()->getCommandMap()->register("CommandConfigKits", new CommandConfigKits($this));
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        $this->saveConfig();
    }
}
