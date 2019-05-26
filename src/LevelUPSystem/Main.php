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
use pocketmine\event\player\PlayerMoveEvent;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getLogger()->info("LevelUP has been enabled! For more contact me at zaydepths@gmail.com");
        $this->profile = new Config($this->getDataFolder() . "profile.yml", Config::YAML, array());
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
    
    public function profileInterface($sender){
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){ //Added the new UI lel -ZZ/ZAY
            $result = $data;
            if ($result == null) {
            }
          switch ($result) {
            case 0:
                        break;            
            }
        });
        $form->setTitle("§l§bProfile");
$form->setContent("§aName§e:§7 " . $sender->getName() . " \n§r§7 \n§r§aLevel§e:§6 " . $this->getLevel($sender) . " \n\n§r§7§aExperience§e:§6 " . $this->getExp($sender) . "\n\n§r§aKills§e:§6 " . $this->getKills($sender) . " \n\n§r§aDeaths§e:§6 " . $this->getDeaths($sender) . "§6");
        $form->addButton("§l§aGo Back", 0);
        $form->sendToPlayer($sender);
    }
    
    public function runLevel($player){
        $exp = $this->getExp($player);
        $expn = $this->getExpCount($player);
        if($this->getLevel($player) == 100){
$player->setDisplayName(C::DARK_GRAY. "§b". C::BOLD. C::RED. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        }
        if($exp >= $expn){
            $this->startLevel($player);
            $this->reduceExp($player, $expn);
            $this->setNamedTag($player);
            $this->addExpCount($player, 42);
            $player->addTitle("§l§3LEVEL §r§e". $this->getLevel($player). "§e");
    }
    
    public function startLevel($player){
        $this->profile->setNested(strtolower($player->getName()).".lvl", $this->profile->getAll()[strtolower($player->getName())]["lvl"] + 1);
        $this->profile->save();
        $this->setNamedTag($player);
    }
    public function setNamedTag($player){
        $player->setDisplayName(C::DARK_GRAY. "§b". C::BOLD. C::AQUA. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $player->save();
    }
  
    public function newProfile($player){
        $this->profile->setNested(strtolower($player->getName()).".lvl", "1");
        $this->profile->setNested(strtolower($player->getName()).".exp", "0");
        $this->profile->setNested(strtolower($player->getName()).".expcount", "100");
        $this->profile->setNested(strtolower($player->getName()).".kills", "0");
        $this->profile->setNested(strtolower($player->getName()).".deaths", "0");
        $this->profile->save();
    }
    
    public function setDeath($player){
         $this->profile->setNested(strtolower($player->getName()).".deaths", $this->profile->getAll()[strtolower($player->getName())]["deaths"] + 1);
         $this->profile->save();
    }
    public function setKill($player){
         $this->profile->setNested(strtolower($player->getName()).".kills", $this->profile->getAll()[strtolower($player->getName())]["kills"] + 1);
         $this->profile->save();
    }
    public function addExp($player, $exp){
        $this->profile->setNested(strtolower($player->getName()).".exp", $this->profile->getAll()[strtolower($player)]["exp"] + $exp);
        $this->profile->save();
    }
    public function addExpCount($player, $exp){
        $this->profile->setNested(strtolower($player->getName()).".expcount", $this->profile->getAll()[strtolower($player->getName())]["expcount"] + $exp);
        $this->profile->save();
    }

    public function getDeaths($player){
        return $this->profile->getAll()[strtolower($player->getName())]["deaths"];
    }
    public function getKills($player){
        return $this->profile->getAll()[strtolower($player->getName())]["kills"];
    }
    public function getExp($player){
        return $this->profile->getAll()[strtolower($player->getName())]["exp"];
    }
    public function getLevel($player){
        return $this->profile->getAll()[strtolower($player->getName())]["lvl"];
    }
    public function getExpCount($player){
        return $this->profile->getAll()[strtolower($player->getName())]["expcount"];
    }

    public function playerJoin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        if(!$this->profile->exists(strtolower($p->getName()))){
            $this->newProfile($p);
        }
        $this->setNamedTag($p);
    }

public function onAuto(PlayerMoveEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->runLevel($player);
}
    
public function onPlace(BlockPlaceEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 1);
    }
    
public function onBreak(BlockBreakEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 1);
    }
    
public function onJoining(PlayerJoinEvent $ev){
        $player = $ev->getPlayer()->getName();
        $this->addExp($player, 10);    
    }
    
public function onKill(PlayerDeathEvent $event) {
        $this->setDeath($event->getEntity());
        if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            $slayer = $event->getEntity()->getLastDamageCause()->getDamager();
            if($slayer instanceof Player) {
                $this->setKill($slayer);
                $this->addExp($slayer, 10);
                $this->runLevel($slayer);
            }
        }
    }

}
