<?php

namespace Raidoxx\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Raidoxx\Main;

class CommandKits extends Command
{

    private Main $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct('Kits', '§7Utilize para ver os Kits!', null, ['Kits']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            $config = $this->plugin->getConfig();
            $itens = $config->get("itens");
            foreach ($itens as $id){
                $itensid = ItemFactory::getInstance()->get($id);
              $sender->getInventory()->addItem($itensid);
            }

        }
    }
}