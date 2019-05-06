<?php

namespace LevelUPSystem;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getLogger()->info("LevelUP has been enabled! For more contact me at zaydepths@gmail.com");
        $this->stats = new Config($this->getDataFolder() . "mini.yml", Config::YAML, array());
        if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        switch (strtolower($command->getName())) {
            case "profile":
                $this->profileInterface($sender);
            break;
            case "levelup":
            $this->initializeLevelConfirm($sender);
            break;
        }
        return true;
    }
    
    public function initializeLevelConfirm($sender){
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){ //Added the new UI lel -ZZ/ZAY
            $result = $data;
            if ($result == null) {
            }
          switch ($result) {
            case 0:
                $sender->sendMessage("§7");
                break;
            case 1:
                $this->runLevel($sender);
                        break;
          }
        });
        $form->setTitle("§l§bLevel UP");
$form->setContent("§eYou have §7" . $this->getExp($sender) . " §eexperience \n\n§r§eNeeded §eexperience§e to §blevelup§e: §7" . $this->getExpCount($sender) . "§6");
        $form->addButton("§cBack", 0);
        $form->addButton("§l§aYES", 1);
        $form->sendToPlayer($sender);
    }
    
    public function profileInterface($sender){
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){ //Added the new UI lel -ZZ/ZAY
            $result = $data;
            if ($result == null) {
            }
          switch ($result) {
            case 0:
                $sender->sendMessage("§6");
                        break;
            case 1:
                $this->initializeLevelConfirm($sender);
            }
        });
        $form->setTitle("§l§bProfile");
$form->setContent("§aName§e:§7 " . $sender->getName() . " \n§r§7 \n§r§aLevel§e:§6 " . $this->getLevel($sender) . " \n\n§r§7§aExperience§e:§6 " . $this->getExp($sender) . " §e/ §l" . $this->getExpCount($sender) . " \n\n§r§aKills§e:§6 " . $this->getKills($sender) . " \n\n§r§aDeaths§e:§6 " . $this->getDeaths($sender) . "§6");
        $form->addButton("§l§aGo Back", 0);
        $form->addButton("§l§bLevelup\n§r§7Click to levelup");
        $form->sendToPlayer($sender);
    }
    
    public function runLevel($player){
        $exp = $this->getExp($player);
        $expn = $this->getExpCount($player);
        if($this->getLevel($player) == 100){
            $player->sendMessage(C::ITALIC. C::RED. "You have reached the max level");
$player->setDisplayName(C::DARK_GRAY. "§b". C::BOLD. C::RED. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        }
        if($exp >= $expn){
            $this->startLevel($player);
            $this->reduceExp($player, $expn);
            $this->setNamedTag($player);
            $this->addExpCount($player, $expn * 1);
            $player->sendMessage(C::YELLOW . "You leveled up to ". $this->getLevel($player). "!");
            $player->addTitle(C::GOLD. "§l§b ". $this->getLevel($player). "§a");
        }else{
            $player->sendMessage(C::RED. "You don't have enough experience to levelup");
$player->sendMessage("§cYou currently only have §e" . $this->getExp($player) . " §cexperience");
        }
    }
    
    public function startLevel($player){
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        $this->stats->save();
        $this->setNamedTag($player);
        $this->getServer()->broadcastMessage("§a". $player->getName(). " §7has level up to §b". $this->getLevel($player) . "§7 .");
    }
    public function setNamedTag($player){
        $player->setDisplayName(C::DARK_GRAY. "§b". C::BOLD. C::AQUA. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $player->save();
    }
    
public function reduceExp($player, $exp){
        $this->stats->setNested(strtolower($player->getName()).".exp", $this->stats->getAll()[strtolower($player->getName())]["exp"] - $exp);
        $this->stats->save();
    }
  
    public function addMembers($player){
        $this->stats->setNested(strtolower($player->getName()).".lvl", "1");
        $this->stats->setNested(strtolower($player->getName()).".exp", "0");
        $this->stats->setNested(strtolower($player->getName()).".expcount", "200");
        $this->stats->setNested(strtolower($player->getName()).".kills", "0");
        $this->stats->setNested(strtolower($player->getName()).".deaths", "0");
        $this->stats->save();
    }
    
    public function setDeath($player){
         $this->stats->setNested(strtolower($player->getName()).".deaths", $this->stats->getAll()[strtolower($player->getName())]["deaths"] + 1);
         $this->stats->save();
    }
    public function setKill($player){
         $this->stats->setNested(strtolower($player->getName()).".kills", $this->stats->getAll()[strtolower($player->getName())]["kills"] + 1);
         $this->stats->save();
    }
    public function addExp($player, $exp){
        $this->stats->setNested(strtolower($player).".exp", $this->stats->getAll()[strtolower($player)]["exp"] + $exp);
        $this->stats->save();
    }
    public function addExpCount($player, $exp){
        $this->stats->setNested(strtolower($player->getName()).".expcount", $this->stats->getAll()[strtolower($player->getName())]["expcount"] + $exp);
        $this->stats->save();
    }

    public function getDeaths($player){
        return $this->stats->getAll()[strtolower($player->getName())]["deaths"];
    }
    public function getKills($player){
        return $this->stats->getAll()[strtolower($player->getName())]["kills"];
    }
    public function getExp($player){
        return $this->stats->getAll()[strtolower($player->getName())]["exp"];
    }
    public function getLevel($player){
        return $this->stats->getAll()[strtolower($player->getName())]["lvl"];
    }
    public function getExpCount($player){
        return $this->stats->getAll()[strtolower($player->getName())]["expcount"];
    }

    public function playerJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        if(!$this->stats->exists(strtolower($p->getName()))){
            $this->addMembers($p);
        }
        $this->setNamedTag($p);
    }
    
public function xp1(BlockPlaceEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 5);
    }
    
public function xp2(BlockBreakEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 5);
    }
    
public function xp3(PlayerJoinEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 18);    

    }
    
public function killAdd(PlayerDeathEvent $event) {
        $this->setDeath($event->getEntity());
        if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            $killer = $event->getEntity()->getLastDamageCause()->getDamager();
            if($killer instanceof Player) {
                $this->setKill($killer);
            }
        }
    }

}
