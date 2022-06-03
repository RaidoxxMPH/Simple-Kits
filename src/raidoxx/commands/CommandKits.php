<?php

namespace Raidoxx\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use Raidoxx\Main;

class CommandKits extends Command
{
    public array $cooldown = [];
    
    private Main $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct('Kits', '§7Utilize para ver os Kits!', null, ['Kits']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {

            $this->SelectWindow($sender);

        }
    }

    private function SelectKit(Player $player){
        $type = "IconsKitsWindow";
        $window = $this->ChooseWindow($type);
        $window->setName("Kits");
        $config = $this->plugin->getConfig()->get("Icons");
        foreach($config as $data){
            $item = ItemFactory::getInstance()->get($data["item"]);
            $item->setCustomName($data["name"]);
            $window->getInventory()->setItem($data["slot"], $item);
        }
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void{
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $inv = $transaction->getAction()->getInventory();
            $config = $this->plugin->getConfig()->get("Kits");
            foreach ($config as $data) {
                if($item->getCustomName() === $data["name"]) {
                    $time = $data["time"];
                    $kit = $data["name"];
                    $this->KITCooldown($data["Itens"], $player, $time, $kit);
                    $transaction->getPlayer()->removeCurrentWindow($inv);
                }
            }
        }));
        $window->send($player);
    }

    private function BasicWindow(Player $player){
        $type = "BasicKits";
        $window = $this->ChooseWindow($type);
        $window->setName("Kits");
        $config = $this->plugin->getConfig()->get("BasicKits");
        foreach($config as $data){
            $item = ItemFactory::getInstance()->get($data["item"]);
            $item->setCustomName($data["name"]);
            $item->setLore([$data["lore"]]);
            $window->getInventory()->setItem($data["slot"], $item);
        }
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void {
            $player = $transaction->getPlayer();
            $kitName = $transaction->getItemClicked()->getCustomName();
            $cfg = $this->plugin->getConfig()->get("BasicKits");
            foreach ($cfg as $data) {
                if ($kitName === $data["name"]) {
                    $time = $data["time"];
                    $itens = $data["itens"];
                    $this->ViewItens($player, $kitName, $time, $itens);
                }
            }
        }));
        $window->send($player);
    }

    private function SelectWindow(Player $player){
        $type = "SelectKitsWindow";
        $window = $this->ChooseWindow($type);
            $window->setName("Kits");
            $config = $this->plugin->getConfig()->get("SelectKits");
            foreach ($config as $data){
                $item = ItemFactory::getInstance()->get($data["item"]);
                $item->setCustomName($data["name"]);
                $item->setLore([$data["lore"]]);
                $window->getInventory()->setItem($data["slot"], $item);
            }
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void{
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $inv = $transaction->getAction()->getInventory();
            $cfg = $this->plugin->getConfig()->get("SelectKits");
            foreach ($cfg as $data){
                if($item->getCustomName() === $data["name"]){
                    $this->switchingWindows($data["name"], $player);
                    $transaction->getPlayer()->removeCurrentWindow($inv);
                }
            }
        }));
            $window->send($player);
    }

    private function SwitchingWindows($kit, $player){
        $data1 = $this->plugin->getConfig()->get("SelectKits")["basics"]["name"];
        $data2 = $this->plugin->getConfig()->get("SelectKits")["vips"]["name"];
        $data3 = $this->plugin->getConfig()->get("SelectKits")["creates"]["name"];
        $data4 = $this->plugin->getConfig()->get("SelectKits")["cash"]["name"];
        if($kit == $data1){
            $this->BasicWindow($player);
        }
        if($kit == $data2){
            $player->sendMessage("§cNão disponivel");
        }
        if($kit == $data3){
            $player->sendMessage("§cNão disponivel");
        }
        if($kit == $data4){
            $player->sendMessage("§cNão disponivel");
        }
    }

    private function ViewItens($player, $kit, $time, $itens){
        $type = "PreviewItens";
        $window = $this->ChooseWindow($type);
        $config = $this->plugin->getConfig()->get("BasicKits");
        $window->setName($config[$kit]["name"]);
        $this->ItensView($config[$kit]["itens"], $window);
        $item0 = ItemFactory::getInstance()->get(262, 0,1);
        $item1 = ItemFactory::getInstance()->get(35, 14, 1);
        $item2 = ItemFactory::getInstance()->get(35, 13, 1);
        $item0->setCustomName("§6Return to Kits Selection");
        $item1->setCustomName("§cClick for cancel");
        $item2->setCustomName("§eClick for get kit");
        $nbt = $item2->getNamedTag();
        $nbt->setString("kitName", $config[$kit]["name"]);
        $item2->setNamedTag($nbt);
        $config = $this->plugin->getConfig()->get("Windows")[$type];
        if($config["type"] === 2){
            $window->getInventory()->setItem(24, $item2);
            $window->getInventory()->setItem(26, $item1);
            $window->getInventory()->setItem(25, $item0);
        }
        if($config["type"] === 3){
            $window->getInventory()->setItem(48, $item2);
            $window->getInventory()->setItem(49, $item0);
            $window->getInventory()->setItem(50, $item1);
        }
        $window->setListener(InvMenu::readonly(listener: function(DeterministicInvMenuTransaction $transaction) : void{
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $inv = $transaction->getAction()->getInventory();
            $data = $this->plugin->getConfig()->get("BasicKits");
            if($item->getCustomName() === "§cClick for cancel"){
                $transaction->getPlayer()->removeCurrentWindow($inv);
            }
            if($item->getCustomName() === "§eClick for get kit"){
                $nbt = $item->getNamedTag();
                $kit = $nbt->getString("kitName");
                $time = $data[$kit]["time"];
                $this->KITCooldown($data[$kit]["itens"], $player, $time, $kit);
                $transaction->getPlayer()->removeCurrentWindow($inv);
            }
            if($item->getCustomName() === "§6Return to Kits Selection"){
                $this->BasicWindow($player);
                $transaction->getPlayer()->removeCurrentWindow($inv);
            }
        }));
        $window->send($player);
    }
    private function ActiveItems($config, $player){
        foreach ($config as $id) {
            $n = explode(":", $id);

            if(array_key_exists(5, $n)) {
                $item_id = $n[0];
                $amount = $n[1];
                $name = $n[2];
                $lore = $n[3];
                $enchantment = $n[4];
                $level = $n[5];
                $nid = ItemFactory::getInstance()->get($item_id, 0, $amount);
                $enchant = StringToEnchantmentParser::getInstance()->parse($enchantment);
                $nid->addEnchantment(new EnchantmentInstance($enchant, $level));
                if(strtolower($name) != "default") {
                    $nid->setCustomName($name);
                }
                if(strtolower($lore) != "no"){
                    $nid->setLore([$lore]);
                }
                if ($player->getInventory()->canAddItem($nid)) {
                    $player->getInventory()->addItem($nid);
                    $player->sendMessage("§aAdd");
                } else {
                    $player->sendMessage("§cYour inventory is full");
                }
            }else{
                $item_id = $n[0];
                $amount = $n[1];
                $name = $n[2];
                $lore = $n[3];
                $nid = ItemFactory::getInstance()->get($item_id, 0, $amount);
                if(strtolower($name) != "default"){
                    $nid->setCustomName($name);
                }
                if(strtolower($lore) != "no"){
                    $nid->setLore([$lore]);
                }
                if ($player->getInventory()->canAddItem($nid)) {
                    $player->getInventory()->addItem($nid);
                } else {
                    $player->sendMessage("§cYour inventory is full");
                }
            }
        }
    }

    private function ItensView($ids, $window): Item
    {
        foreach ($ids as $id) {
            $n = explode(":", $id);

            if(array_key_exists(5, $n)) {
                $item_id = $n[0];
                $amount = $n[1];
                $name = $n[2];
                $lore = $n[3];
                $enchantment = $n[4];
                $level = $n[5];
                $item = ItemFactory::getInstance()->get($item_id, 0, $amount);
                $enchant = StringToEnchantmentParser::getInstance()->parse($enchantment);
                $item->addEnchantment(new EnchantmentInstance($enchant, $level));
                if(strtolower($name) != "default") {
                    $item->setCustomName($name);
                }
                if(strtolower($lore) != "no"){
                    $item->setLore([$lore]);
                }
            }else{
                $item_id = $n[0];
                $amount = $n[1];
                $name = $n[2];
                $lore = $n[3];
                $item = ItemFactory::getInstance()->get($item_id, 0, $amount);
                if(strtolower($name) != "default"){
                    $item->setCustomName($name);
                }
                if(strtolower($lore) != "no"){
                    $item->setLore([$lore]);
                }
            }
            $window->getInventory()->addItem($item);
        }
        return $item;
    }

    private function KITCooldown($config, $player, int $time, $kit){
       if(!isset($this->cooldown[$player->getName()][$kit])){
           $this->cooldown[$player->getName()][$kit] = time() + $time;
           $this->ActiveItems($config, $player);
       } else {
           if(time() < $this->cooldown[$player->getName()][$kit]){
            $remaining = $this->cooldown[$player->getName()][$kit] - time();
            $player->sendMessage("§cYou just can take in: §e".gmdate("H:i:s", $remaining));
           } else {
               unset($this->cooldown[$player->getName()][$kit]);
               $this->ActiveItems($config, $player);
               $this->cooldown[$player->getName()][$kit] = time() + $time;
           }
       }
    }

    private function ChooseWindow($type): InvMenu
    {
        $config = $this->plugin->getConfig()->get("Windows")[$type];
                if($config["type"] === 1){
                    $window = InvMenu::create(InvMenu::TYPE_HOPPER);
                }
                if($config["type"] === 2){
                    $window = InvMenu::create(InvMenu::TYPE_CHEST);
                }
                if($config["type"] === 3){
                    $window = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                }
        return $window;
    }
}