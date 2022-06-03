<?php

namespace Raidoxx\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Raidoxx\Main;

class CommandConfigKits extends \pocketmine\command\Command
{
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct('KitsConfig', '§7Utilize para ver os Kits!', null, ['KitsConfig','kitsconfig','kc']);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {

            $this->ConfigWindow($sender);

        }
    }

    private function ConfigWindow(Player $player){
        $window = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $window->setName("Config Kits | BY: RDX");
        $config = $this->plugin->getConfig()->get("Windows");
        foreach($config as $data){
            $item = ItemFactory::getInstance()->get(339);
            $item->setCustomName($data["name"]);
            if($data["type"] == 1){
                $item->setLore(["Type: §bHooper"]);
            }
            if($data["type"] == 2){
                $item->setLore(["Type: §bChest"]);
            }
            if($data["type"] == 3){
                $item->setLore(["Type: §bDouble Chest"]);
            }
            $window->getInventory()->addItem($item);
        }
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void{
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $inv = $transaction->getAction()->getInventory();
            $config = $this->plugin->getConfig()->get("Windows");
            foreach($config as $data){
                if($item->getCustomName() === $data["name"]){
                    $name = $data["name"];
                    $this->SelectType($player, $name);
                }
            }
        }));
        $window->send($player);
    }
    private function SelectType(Player $player, $name){
        $window = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $window->setName("Config Kits | BY: RDX");
        $config = $this->plugin->getConfig()->get("Windows");
        $hopper = ItemFactory::getInstance()->get(410);
        $hopper->setCustomName("Size Hopper");
        $this->setNBT($hopper, $name);
        $chest = ItemFactory::getInstance()->get(54);
        $chest->setCustomName("Size Chest");
        $this->setNBT($chest, $name);
        $chest2 = ItemFactory::getInstance()->get(54);
        $chest2->setCustomName("Size Double Chest");
        $this->setNBT($chest2, $name);
        $window->getInventory()->setItem(20, $hopper);
        $window->getInventory()->setItem(22, $chest);
        $window->getInventory()->setItem(24, $chest2);
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void{
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $nbt = $item->getNamedTag();
            $config = $this->plugin->getConfig();
            $all = $config->getAll();
            if($item->getCustomName() === "Size Hopper"){
                $all["Windows"][$nbt->getString("configName")]["type"] = 1;
                var_dump($nbt->getString("configName"));
                $player->sendMessage("Alterado para 1");
            }
            if($item->getCustomName() === "Size Chest"){
                $all["Windows"][$nbt->getString("configName")]["type"] = 2;
                var_dump($nbt->getString("configName"));
                $player->sendMessage("Alterado para 2");
            }
            if($item->getCustomName() === "Size Double Chest"){
                $all["Windows"][$nbt->getString("configName")]["type"] = 3;
                var_dump($nbt->getString("configName"));
                $player->sendMessage("Alterado para 3");
            }
            $config->setAll($all);
            var_dump($all);
           $config->save();
        }));
        $window->send($player);
    }

    private function setNBT($item, $config)
    {
            $nbt = $item->getNamedTag();
            $nbt->setString("configName", $config);
            $item->setNamedTag($nbt);
    }
}