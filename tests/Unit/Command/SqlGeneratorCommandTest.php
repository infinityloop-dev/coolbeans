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
        CREATE TABLE `simple_bean_2`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,

            PRIMARY KEY (`id`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `simple_bean_attribute`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,

            CONSTRAINT `unique_SimpleBeanAttribute_col3` UNIQUE (`col3`),

            PRIMARY KEY (`id`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `simple_bean_class_attribute`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,

            CONSTRAINT `unique_SimpleBeanClassAttribute_0` UNIQUE (`col4`, `col5`),

            PRIMARY KEY (`id`)
        )
            CHARSET = `cz_charset`
            COLLATE = `cz_collation`;
        
        CREATE TABLE `simple_bean_class_attribute_2`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,
        
            CONSTRAINT `unique_SimpleBeanClassAttribute2_0` UNIQUE (`col4`, `col5`),
            CONSTRAINT `unique_SimpleBeanClassAttribute2_1` UNIQUE (`col4`, `col6`),

            PRIMARY KEY (`id`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `attribute_bean`(
            `col2`             DATE             NOT NULL DEFAULT NOW(),
            `col3`             TIME             NOT NULL DEFAULT NOW(),
            `col4`             BIGINT           NOT NULL,
            `col5`             INT(44)          NOT NULL DEFAULT 1 COMMENT 'Some random comment',
            `col6`             DECIMAL(1, 3)    NOT NULL DEFAULT 1.005,
            `simple_bean_2_id` INT(11) UNSIGNED NOT NULL,
            `col8`             DOUBLE(16, 2)    NOT NULL,
            `col9_id`          INT(11) UNSIGNED NOT NULL,
            `col10_id`         INT(11) UNSIGNED NOT NULL,
            `code`             VARCHAR(255)     NOT NULL,
            `id`               INT(11) UNSIGNED NOT NULL,

            INDEX `index_AttributeBean_0` (`col4` ASC, `col5` DESC, `col6` ASC),
            INDEX `index_AttributeBean_col5_0` (`col5` ASC),
            INDEX `index_AttributeBean_col6_0` (`col6` ASC),
            INDEX `index_AttributeBean_col8_0` (`col8` DESC),

            FOREIGN KEY (`simple_bean_2_id`) REFERENCES `simple_bean_2`(`id`),
            FOREIGN KEY (`col9_id`) REFERENCES `simple_bean_2`(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`col10_id`) REFERENCES `simple_bean_2`(`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,

            CONSTRAINT `unique_AttributeBean_0` UNIQUE (`col2`, `col3`),
            CONSTRAINT `unique_AttributeBean_1` UNIQUE (`col4`, `col5`, `col6`),
            CONSTRAINT `unique_AttributeBean_col4` UNIQUE (`col4`),
            CONSTRAINT `unique_AttributeBean_col5` UNIQUE (`col5`),
            CONSTRAINT `unique_AttributeBean_col6` UNIQUE (`col6`),

            PRIMARY KEY (`code`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`
            COMMENT = 'Some random comment';
        
        CREATE TABLE `simple_bean_1`(
            `id`               INT(11) UNSIGNED        NOT NULL AUTO_INCREMENT,
            `col3`             VARCHAR(255)            NOT NULL,
            `col4`             VARCHAR(255),
            `col5`             VARCHAR(255)                     DEFAULT NULL,
            `col6`             VARCHAR(255)                     DEFAULT 'default',
            `simple_bean_2_id` INT(11) UNSIGNED        NOT NULL,
            `col8`             DATETIME                NOT NULL,
            `col9`             DATETIME                NOT NULL,
            `col10`            INT(11)                 NOT NULL DEFAULT 5,
            `col11`            DOUBLE                  NOT NULL DEFAULT 0.005,
            `col12`            ENUM('abc','bca','xyz') NOT NULL DEFAULT 'abc',
            `col13`            TINYINT(7)              NOT NULL DEFAULT 22,
            `col14`            VARCHAR(64)             NOT NULL DEFAULT 'abc',
            `col15`            INT(11)                 NOT NULL DEFAULT 0,
            `col16`            JSON                    NOT NULL,
        
            FOREIGN KEY (`simple_bean_2_id`) REFERENCES `simple_bean_2`(`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,

            PRIMARY KEY (`id`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        EOL;

        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $result = $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../TestBean/',
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

    public function testUndefinedProperty() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\UnknownColumnInColumnArray::class);
        $this->expectExceptionMessage('Column [invalid] given in column array doesnt exist in Bean InvalidBean.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/UndefinedProperty/',
        ]);
    }

    public function testMissingPrimaryKey() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\UnknownColumnInColumnArray::class);
        $this->expectExceptionMessage('Column [id] given in column array doesnt exist in Bean InvalidBean.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/MissingPrimaryKey/',
        ]);
    }

    public function testPrimaryKeyAttributeMissingColumn() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\UnknownColumnInColumnArray::class);
        $this->expectExceptionMessage('Column [unknown] given in column array doesnt exist in Bean InvalidBean.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/PrimaryKeyAttributeMissingColumn/',
        ]);
    }
}
