<?php

declare(strict_types=1);

namespace NPCDeath\XxCreditIsGoodXx;

use pocketmine\scheduler\Task;

class NPCDeathClearTask extends Task{

	/** @var NPCDeathEntity $entity */
	private $entity;

	public function __construct(NPCDeathEntity $entity){
		$this->entity = $entity;
	}

	public function onRun(int $tick) : void{
		$this->entity->flagForDespawn();
	}
}
