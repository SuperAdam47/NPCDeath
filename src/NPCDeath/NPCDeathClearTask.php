<?php

declare(strict_types=1);

namespace DeathNPC;

use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class DeathNPCClearTask extends PluginTask{

    /** @var Entity $entity */
    private $entity;
    /** @var Player $player */
    private $player;
    
    public function __construct(Main $main, Entity $entity, Player $player){
        $this->entity = $entity;
        $this->player = $player;
        parent::__construct($main);
    }
    
    public function onRun(int $tick) : void{
        if($this->entity instanceof DeathNPCEntity){
            if($this->entity->getNameTag() === "RIP " . $this->player->getName() . " died here") $this->entity->close();
        }
    }
}
