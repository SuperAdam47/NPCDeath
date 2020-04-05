<?php

declare(strict_types=1);

namespace NPCDeath\XxCreditIsGoodXx;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

	private const NPC_SAVE_ID = "NPCDdeath:dead_npc";

	public function onEnable() : void{
		Entity::registerEntity(NPCDeathEntity::class, true, [self::NPC_SAVE_ID]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param PlayerDeathEvent $event
	 * @return void
	 */
	public function onDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();

		$pos = $player->floor();
		$skin = $player->getSkin();

		$nbt = Entity::createBaseNBT($pos);
		$nbt->setTag(new CompoundTag("Skin", [
			new StringTag("Name", $skin->getSkinId()),
			new ByteArrayTag("Data", $skin->getSkinData()),
			new ByteArrayTag("CapeData", $skin->getCapeData()),
			new StringTag("GeometryName", $skin->getGeometryName()), new ByteArrayTag("GeometryData", $skin->getGeometryData())
		]));

		/** @var NPCDeathEntity $npc */
		$npc = Entity::createEntity(self::NPC_SAVE_ID, $player->getLevel(), $nbt);
		$npc->setCanSaveWithChunk(false);

		$npc->getDataPropertyManager()->setBlockPos(Human::DATA_PLAYER_BED_POSITION, $pos);
		$npc->setPlayerFlag(Human::DATA_PLAYER_FLAG_SLEEP, true);

		$npc->setNameTag($player->getName());
		$npc->setNameTagAlwaysVisible(true);

		$npc->spawnToAll();
		$this->getScheduler()->scheduleDelayedTask(new NPCDeathClearTask($npc), 300);
	}

	/**
	 * @param EntityDamageEvent $event
	 * @return void
	 */
	public function onDamage(EntityDamageEvent $event) : void{
		if($event->getEntity() instanceof NPCDeathEntity){
			$event->setCancelled();
		}
	}
}
