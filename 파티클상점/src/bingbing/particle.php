<?php
namespace bingbing;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\block\Lava;
use pocketmine\math\Vector3;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\event\player\PlayerInteractEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\particle\LavaParticle;

class particle extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->shopdb = new Config($this->getDataFolder() . "shop.yml", Config::YAML, [
            "shop" => array(),
            "change" => array(),
            "shops" => 0
        ]);

        $this->shop = $this->shopdb->getAll();

        $this->namedb = new Config($this->getDataFolder() . "player.yml", Config::YAML);
        $this->name = $this->namedb->getAll();
    }

    public function join(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $this->name[$name] = [];
        $this->name[$name]["particle"] = "";
        $this->name[$name]["list"] = array();
        $this->name[$name]["cash"] = 0;

        /*
         *
         */
    }

    public function move(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();

        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $blockId = $player->getLevel()->getBlockIdAt($x, $y, $z);
        switch ($this->name[$name]["particle"]) {
            case "heart":

                $this->heart($player);
                break;
            case "dust":
                $this->dust($player);
                break;
            case "lava":
                $this->lava($player);
                break;
            case "cross":
                $this->cross($player);
                break;
            case "explore":
                $this->explore($player);
                break;
            case "flame":
                $this->flame($player);
                break;
        }
    }

    public function message(SignChangeEvent $event)
    {
        if ($event->getPlayer()->isOp()) {
            if ($event->getLine(0) == "???????????????" and is_numeric($event->getLine(2))) {
                if ($event->getLine(1) == "heart") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??d???????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    array_push($this->shop["shop"], array(
                        "heart",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    // ModalFormRequestPacket
                    $this->shop["shops"] = + 1;
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    $this->save();
                } else if ($event->getLine(1) == "lava") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??d???????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    array_push($this->shop["shop"], array(
                        "lava",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    $this->save();
                } else if ($event->getLine(1) == "dust") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??d???????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    array_push($this->shop["shop"], array(
                        "dust",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    $this->save();
                } 
                else if ($event->getLine(1) == "cross") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??d??????????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    array_push($this->shop["shop"], array(
                        "cross",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    $this->save();
                } else if ($event->getLine(1) == "explore") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??a???????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    array_push($this->shop["shop"], array(
                        "explore",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    $this->save();
                } else if ($event->getLine(1) == "flame") {
                    $event->setLine(0, "??f[??a???????????e????????f]");
                    $event->setLine(1, "??a???????????????");
                    $event->setLine(2, "??f" . $event->getLine(2) . " ??e???");
                    array_push($this->shop["shop"], array(
                        "flame",
                        $event->getLine(2),
                        $event->getBlock()->getFloorX(),
                        $event->getBlock()->getFloorY(),
                        $event->getBlock()->getFloorZ()
                    ));
                    $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                    $this->save();
                }
            }
            if ($event->getLine(0) == "??????" and is_numeric($event->getLine(1)) and is_numeric($event->getLine(2))) {

                $event->setLine(0, "??f[??e????????f]");
                $event->setLine(1, "??a???:" . "??f" . $event->getLine(1) . " ??e???");
                $event->setLine(2, "??a??????:" . "??f" . $event->getLine(2) . " ??e???");
                array_push($this->shop["change"], array(
                    $event->getLine(1),
                    $event->getLine(2),
                    $event->getBlock()->getFloorX(),
                    $event->getBlock()->getFloorY(),
                    $event->getBlock()->getFloorZ()
                ));
                $event->getPlayer()->sendMessage("??????????????? ??????????????????");
                $this->save();
            }
        }
    }

    public function touch(PlayerInteractEvent $event)
    {
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        $player = $event->getPlayer()->getName();
        if ($event->getBlock()->getId() == "323" or $event->getBlock()->getId() == "63" or $event->getBlock()->getId() == "68") {
            for ($a = 0; $a < count($this->shop["shop"]); $a ++) {
                if ($x == $this->shop["shop"][$a][2] and $y == $this->shop["shop"][$a][3] and $z == $this->shop["shop"][$a][4]) {
                    if ($this->name[$player]["cash"] >= $this->shop["shop"][$a][1]) {
                        array_push($this->name[$player]["list"], $this->shop["shop"][$a][0]);
                        $this->name[$player]["cash"] = - $this->shop["shop"][$a][1];
                        $event->getPlayer()->sendMessage("??????????????? ????????????");
                        $this->save();
                        break;
                    } else if ($this->name[$player]["cash"] < $this->shop["shop"][$a][1]) {
                        $this->save();
                        $event->getPlayer()->sendMessage("????????? ???????????????");
                    }
                } else if ($x == $this->shop["change"][$a][2] and $y == $this->shop["change"][$a][3] and $z == $this->shop["change"][$a][4] and EconomyAPI::getInstance()->myMoney($player) >= $this->shop["change"][$a][0]) {
                    $this->name[$player]["cash"] = + $this->shop["shop"][$a][1];
                    EconomyAPI::getInstance()->reduceMoney($player, $this->shop["shop"][$a][0]);
                    $event->getPlayer()->sendMessage("??????????????? ????????????");
                    $this->save();
                } else if (EconomyAPI::getInstance()->myMoney($player) < $this->shop["change"][$a][0]) {
                    $this->save();
                    $event->getPlayer()->sendMessage("?????? ???????????????");
                }
            }
        }
    }

    public function break(BlockBreakEvent $event)
    {
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($event->getBlock()->getId() == "323" or $event->getBlock()->getId() == "63" or $event->getBlock()->getId() == "68") {
            for ($a = 0; $a < count($this->shop["shop"]); $a ++) {
                if ($x == $this->shop["shop"][$a][2] and $y == $this->shop["shop"][$a][3] and $z == $this->shop["shop"][$a][4]) {
                    unset($this->shop["shop"][$a]);
                    break;
                } else if ($x == $this->shop["change"][$a][2] and $y == $this->shop["change"][$a][3] and $z == $this->shop["change"][$a][4]) {
                    unset($this->shop["change"][$a]);
                    break;
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $name = $sender->getName();
        if ($command->getName() == "?????????") {
            if ($args[0] == "??????") {
                for ($a = 0; $a < count($this->shop["shop"]); $a ++) {
                    if ($this->name[$name]["list"][$a] == $args[1]) {
                        $this->name[$name]["particle"] = $args[1];
                        break;
                    } else {
                        $sender->sendMessage("?????? ???????????? ?????? ???????????? ????????????");
                        break;
                    }
                }
            }
            if ($args[0] == "??????") {
                if (! empty($this->name[$name]["list"])) {
                    $sender->sendMessage("==========???????????????===========");

                    for ($a = 0; $a < count($this->name[$name]["list"]); $a ++) {
                        $b = $a + 1;
                        $sender->sendMessage("??f[??b???????????f]" . $b . " ????????d" . $this->name[$name]["list"][$a] . " ?????????");
                    }
                    $sender->sendMessage("===========================");
                } else {
                    $sender->sendMessage("?????? ???????????? ?????? ???????????? ????????????");
                }
            }
        }
    }

    public function heart(Player $player)
    {
        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $player->getLevel()->addParticle(new HeartParticle(new Vector3($x, $y + 0.2, $z)));
    }

    public function cross(Player $player)
    {
        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $player->getLevel()->addParticle(new CriticalParticle(new Vector3($x, $y + 0.2, $z)));
    }

    public function dust(Player $player)
    {
        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $player->getLevel()->addParticle(new DustParticle(new Vector3($x, $y + 0.2, $z)));
    }

    public function lava(Player $player)
    {
        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $player->getLevel()->addParticle(new LavaParticle(new Vector3($x, $y + 0.2, $z)));
    }

    public function flame(Player $player)
    {
        $x = $player->x;
        $y = $player->y;
        $z = $player->z;
        $player->getLevel()->addParticle(new FlameParticle(new Vector3($x, $y + 0.2, $z)));
    }

    public function save()
    {
        $this->namedb->setAll($this->name);
        $this->namedb->save();

        $this->shopdb->setAll($this->shop);
        $this->shopdb->save();
    }

    public function onDisable()
    {
        $this->save();
    }
}