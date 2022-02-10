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
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `simple_bean_attribute`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,
        
            CONSTRAINT `unique_simple_bean_attribute_col3` UNIQUE (`col3`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `simple_bean_class_attribute`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,
        
            CONSTRAINT `unique_simple_bean_class_attribute_col4_col5` UNIQUE (`col4`,`col5`)
        )
            CHARSET = `cz_charset`
            COLLATE = `cz_collation`;
        
        CREATE TABLE `simple_bean_class_attribute_2`(
            `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `col3` VARCHAR(255)     NOT NULL,
            `col4` VARCHAR(255),
            `col5` VARCHAR(255)              DEFAULT NULL,
            `col6` VARCHAR(255)              DEFAULT 'default',
            `col8` DATETIME         NOT NULL,
            `col9` DATETIME         NOT NULL,
        
            CONSTRAINT `unique_simple_bean_class_attribute_2_col4_col5` UNIQUE (`col4`,`col5`),
            CONSTRAINT `unique_simple_bean_class_attribute_2_col4_col6` UNIQUE (`col4`,`col6`)
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`;
        
        CREATE TABLE `attribute_bean`(
            `col2`             DATE             NOT NULL DEFAULT NOW(),
            `col3`             TIME             NOT NULL DEFAULT NOW(),
            `col4`             BIGINT           NOT NULL,
            `col5`             INT(44)          NOT NULL DEFAULT 1 COMMENT 'Some random comment',
            `col6`             DECIMAL(1, 3)    NOT NULL DEFAULT '1.005',
            `simple_bean_2_id` INT(11) UNSIGNED NOT NULL,
            `col8`             DOUBLE(16, 2)    NOT NULL,
            `col9_id`          INT(11) UNSIGNED NOT NULL,
            `col10_id`         INT(11) UNSIGNED NOT NULL,
        
            INDEX `attribute_bean_col5_index` (`col5`),
            INDEX `attribute_bean_col6_index` (`col6` ASC),
            INDEX `attribute_bean_col8_index` (`col8` DESC),
            INDEX `attribute_bean_col4_col5_col6_index` (`col4`,`col5` DESC,`col6` ASC),
        
            CONSTRAINT `unique_attribute_bean_col2_col3` UNIQUE (`col2`,`col3`),
            CONSTRAINT `unique_attribute_bean_col4_col5_col6` UNIQUE (`col4`,`col5`,`col6`),
        
            CONSTRAINT `unique_attribute_bean_col4` UNIQUE (`col4`),
            CONSTRAINT `unique_attribute_bean_col5` UNIQUE (`col5`),
            CONSTRAINT `unique_attribute_bean_col6` UNIQUE (`col6`),
        
            FOREIGN KEY (`simple_bean_2_id`) REFERENCES `simple_bean_2`(`id`),
            FOREIGN KEY (`col9_id`) REFERENCES `simple_bean_2`(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`col10_id`) REFERENCES `simple_bean_2`(`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        )
            CHARSET = `utf8mb4`
            COLLATE = `utf8mb4_general_ci`
            COMMENT = 'Some random comment';
        
        CREATE TABLE `simple_bean_1`(
            `id`               INT(11) UNSIGNED        NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
            `col13`            TINYINT(7)              NOT NULL DEFAULT '22',
            `col14`            VARCHAR(64)             NOT NULL DEFAULT 'abc',
        
            FOREIGN KEY (`simple_bean_2_id`) REFERENCES `simple_bean_2`(`id`)
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

    public function testDuplicateColumns() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\ClassUniqueConstraintDuplicateColumns::class);
        $this->expectExceptionMessage('Found duplicate columns defined in ClassUniqueConstraint attribute.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/DuplicateColumns/',
        ]);
    }

    public function testUndefinedProperty() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\ClassUniqueConstraintUndefinedProperty::class);
        $this->expectExceptionMessage('Property with name "invalid" given in ClassUniqueConstraint is not defined.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/UndefinedProperty/',
        ]);
    }

    public function testColumnCount() : void
    {
        $application = new \Symfony\Component\Console\Application();
        $application->addCommands([new \CoolBeans\Command\SqlGeneratorCommand()]);

        $command = $application->find('sqlGenerator');
        $commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

        $this->expectException(\CoolBeans\Exception\InvalidClassUniqueConstraintColumnCount::class);
        $this->expectExceptionMessage('ClassUniqueConstraint expects at least two column names.');

        $commandTester->execute([
            'command' => 'sqlGenerator',
            'source' => __DIR__ . '/../InvalidBean/ColumnCount/',
        ]);
    }
}
