<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Command;

final class SqlGeneratorCommandTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    protected string $output;

    public function mockLog(string $output) : void
    {
        $this->output = $output;
    }

    public function testSimple() : void
    {
        $expected = <<<'EOL'
        CREATE TABLE `attribute_bean`(
            `col2` DATE         NOT NULL DEFAULT NOW(),
            `col3` TIME         NOT NULL DEFAULT NOW(),
            `col4` BIGINT       NOT NULL,
            `col5` INT(44)      NOT NULL DEFAULT "1",
            `col6` DECIMAL(1,3) NOT NULL DEFAULT "1",
        )
            CHARSET = `utf8mb4`
            COLLATE `utf8mb4_general_ci`;

        CREATE TABLE `simple_bean`(
            `id`      INT(11) UNSIGNED NOT NULL AUTOINCREMENT,
            `col3`    VARCHAR(255)     NOT NULL,
            `col4`    VARCHAR(255),
            `col5`    VARCHAR(255)              DEFAULT NULL,
            `col6`    VARCHAR(255)              DEFAULT "default",
            `col7_id` INT(11) UNSIGNED NOT NULL,
            `col8`    DATETIME         NOT NULL,
            `col9`    DATETIME         NOT NULL,
        
            FOREIGN KEY (`col7_id`) REFERENCES `col7`(`id`),
        )
            CHARSET = `utf8mb4`
            COLLATE `utf8mb4_general_ci`;
        EOL;

        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\BeanWithoutPublicProperty::class);
        $this->expectExceptionMessage('Bean TestBean has no public property.');

        $result = $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => '/../../tests/',
        ]);

        self::assertSame(0, $result);
        self::assertSame(
            $expected,
            $commandTester->getDisplay(),
        );
    }

    public function testSettings() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');

        self::assertSame('Converts Beans into SQL.', $command->getDescription());
        self::assertSame('sqlGenerator', $command->getName());
    }
}
