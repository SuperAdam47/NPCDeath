<?php

declare(strict_types=1);

namespace NPCDeath\XxCreditIsGoodXx;

use pocketmine\nbt\tag\{

    CompoundTag, ListTag, DoubleTag, FloatTag
};
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

class Main extends PluginBase implements Listener{

    public function onEnable() : void{
        Entity::registerEntity(NPCDeathEntity::class, true);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
     
    public function onDeath(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $player->getX()),
                new DoubleTag("", $player->getY() - 1),
                new DoubleTag("", $player->getZ())
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", 2),
                new FloatTag("", 2)
            ])
        ]);
        $nbt->setTag($player->namedtag->getTag("Skin"));
        $npc = new NPCDeathEntity($player->getLevel(), $nbt);
        $npc->getDataPropertyManager()->setBlockPos(NPCDeathEntity::DATA_PLAYER_BED_POSITION, new Vector3($player->getX(), $player->getY(), $player->getZ()));
        $npc->setPlayerFlag(NPCDeathEntity::DATA_PLAYER_FLAG_SLEEP, true);
        $npc->setNameTag("RIP " . $player->getName() . " died here");
        $npc->setNameTagAlwaysVisible(false);
        $npc->spawnToAll();
        $this->getServer()->getScheduler()->scheduleDelayedTask(new DeathNPCClearTask($this, $npc, $player), 3600);
    }
    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof NPCDeathEntity) $event->setCancelled(true);
    }
}
