<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

final class CoolbeansExtension extends \Nette\DI\CompilerExtension
{
	public function beforeCompile() : void
	{
		$builder = $this->getContainerBuilder();

        foreach ($builder->findByType(\Nette\Database\Connection::class) as $definition) {
            \assert($definition instanceof \Nette\DI\Definitions\ServiceDefinition);

            $definition->setFactory(new \Nette\DI\Definitions\Statement(Connection::class, $definition->getFactory()->arguments));
        }
	}
}
