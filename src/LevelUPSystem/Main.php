<?php

namespace LevelUPSystem;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\PlayerInteractEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use jojoe77777\FormAPI;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Main extends PluginBase implements Listener {
    
    public function onEnable(){
        $this->getLogger()->info("LevelUP-System has been enabled");
$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->stats = new Config($this->getDataFolder() . "stats.yml", Config::YAML, array());
        if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool {
        switch (strtolower($command->getName())) {
            case "profile":
                $this->profileInterface($sender);
            break;
       
            
            
            case "addexp":
            if(isset($args[0]) && isset($args[1]) && is_numeric($args[1])){
                $this->addExp($args[0], $args[1]);
                return true;
                break;
            }
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
                break;
            
            case 1:
                $this->runLevel($sender);
                break;
          }
        });
        $form->setTitle("§7Confirmation");
$form->setContent("§eYou have §7" . $this->getExp($sender) . " §eexperience \n\n§r§eNeeded §eexperience§e to §blevelup§e: §7" . $this->getExpCount($sender) . "§6");
        $form->addButton("§4BACK", 0);
        $form->addButton("§aYes", 1);
        
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
                        break;
            case 1:
                  $this->initializeLevelConfirm($sender);
                        break;
            }
        });
        $form->setTitle("§l§bProfile");
        $form->setContent("§aName§e:§7 " . $sender->getName() . " \n\n§r§aLevel§e:§6 " . $this->getLevel($sender) . "\n\n§aTier§e:§6 " . $this->getTier($sender) . "\n\n§aCoins§e: §6" . $this->eco->mymoney($sender) . "\n\n§r§aKills§e:§6 " . $this->getKills($sender) . " \n\n§r§aDeaths§e:§6 " . $this->getDeaths($sender) . "§6");
        $form->addButton("§cEXIT", 0);
        $form->addButton("§aLevel Up\n§r§7Click to levelup", 1);

        $form->sendToPlayer($sender);
    }
    
    public function runLevel($player){
        $exp = $this->getExp($player);
        $expn = $this->getExpCount($player);
        if($this->getLevel($player) == 100){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l". $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        }
if($this->getLevel($player) == 5){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l" . $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $this->stats->setNested(strtolower($player->getName()).".tier", $this->stats->getAll()[strtolower($player->getName())]["tier"] + 1);
        $this->stats->save();
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        }
if($this->getLevel($player) == 10){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l" . $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $this->stats->setNested(strtolower($player->getName()).".tier", $this->stats->getAll()[strtolower($player->getName())]["tier"] + 1);
        $this->stats->save();
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        }
if($this->getLevel($player) == 20){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l" . $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $this->stats->setNested(strtolower($player->getName()).".tier", $this->stats->getAll()[strtolower($player->getName())]["tier"] + 1);
        $this->stats->save();
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        }
if($this->getLevel($player) == 30){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l" . $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $this->stats->setNested(strtolower($player->getName()).".tier", $this->stats->getAll()[strtolower($player->getName())]["tier"] + 1);
        $this->stats->save();
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        }
if($this->getLevel($player) == 50){
$player->setDisplayName(C::DARK_GRAY. "§eTier §l" . $this->getTier($player) . " §r§b". C::BOLD. C::GOLD. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $this->stats->setNested(strtolower($player->getName()).".tier", $this->stats->getAll()[strtolower($player->getName())]["tier"] + 1);
        $this->stats->save();
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        }
        if($exp >= $expn){
            $this->startLevel($player);
            $this->reduceExp($player, $expn);
            $this->setTag($player);
            $this->addExpCount($player, 32);
            $player->addTitle(C::GOLD. "§l§b ", "§l§3LEVEL §r§a". $this->getLevel($player). " §a§l", 1, 100, 50);
        } else {
            $player->sendMessage("§cYou don't have enough experience to levelup!");
        }
        }
    
    public function startLevel($player){
        $this->stats->setNested(strtolower($player->getName()).".lvl", $this->stats->getAll()[strtolower($player->getName())]["lvl"] + 1);
        $this->stats->save();
        $this->setTag($player);
    }
    public function setTag($player){
        $player->setDisplayName(C::DARK_GRAY. "§eTier §l". $this->getTier($player) . "§r§b" . C::BOLD. C::AQUA. "" . $this->getLevel($player) . C::AQUA. "§7§r ". C::GREEN. $player->getName());
        $player->save();
    }
    
public function reduceExp($player, $exp){
        $this->stats->setNested(strtolower($player->getName()).".exp", $this->stats->getAll()[strtolower($player->getName())]["exp"] - $exp);
        $this->stats->save();
    }
  
    public function addMembers($player){
        $this->stats->setNested(strtolower($player->getName()).".lvl", "1");
        $this->stats->setNested(strtolower($player->getName()).".tier", "0");
        
        $this->stats->setNested(strtolower($player->getName()).".exp", "0");
        $this->stats->setNested(strtolower($player->getName()).".expcount", "47");
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
        $this->stats->setNested(strtolower($player->getName()).".exp", $this->stats->getAll()[strtolower($player->getName())]["exp"] + $exp);
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
public function getTier($player){
        return $this->stats->getAll()[strtolower($player->getName())]["tier"];
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
        $this->setTag($p);
    }
    
    public function lobbyExp($player){
      $player->sendMessage("§l§eINFO §r§7§l» §r§cYou can't break or place blocks here!");
    }
    
public function xp1(BlockPlaceEvent $ev){
$player = $ev->getPlayer();
        $this->addExp($player, 0.2);
    }
    
public function xp2(BlockPlaceEvent $ev){
$player = $ev->getPlayer();
        $this->addExp($player, 0.2);
    }
    

    public function killMsg($sender){
        $sender->sendMessage("§a§l+10 Exp");
}
    
public function killAddExp(PlayerDeathEvent $event) {
        $this->setDeath($event->getEntity());
        if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            $killer = $event->getEntity()->getLastDamageCause()->getDamager();
            if($killer instanceof Player) {
                $this->setKill($killer);
                $this->addExp($killer, 10);
                $this->killMsg($killer);
            }
        }
    }
}
            
 




