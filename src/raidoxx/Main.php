<?php

namespace Raidoxx;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use Raidoxx\commands\CommandKits;

class Main extends PluginBase implements Listener
{
    public function onEnable():void
    {
        $this->getLogger()->info("§eSIMPLE KITS");

        $this->getServer()->getCommandMap()->register("CommandKits", new CommandKits($this));

        $this->saveConfig();
    }
}


