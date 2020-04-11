<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\Decorator;

final class TCommonTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetName() : void
    {
        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getName')->withNoArgs()->andReturn('name');

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals('name', $commonInstance->getName());
    }

    public function testInsert() : void
    {
        $data = ['name' => 'Shrek'];

        $insert = new \CoolBeans\Result\Insert(new \CoolBeans\PrimaryKey\IntPrimaryKey(1));

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('insert')->with($data)->andReturn($insert);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($insert, $commonInstance->insert($data));
    }

    public function testInsertMultiple() : void
    {
        $data = [['name' => 'Shrek'], ['name' => 'Fiona']];

        $insertMultiple = new \CoolBeans\Result\InsertMultiple([
            new \CoolBeans\PrimaryKey\IntPrimaryKey(1),
            new \CoolBeans\PrimaryKey\IntPrimaryKey(2)
        ]);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('insertMultiple')->with($data)->andReturn($insertMultiple);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($insertMultiple, $commonInstance->insertMultiple($data));
    }

    public function testUpdate() : void
    {
        $data = ['name' => 'Shrek'];
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(1);

        $update = new \CoolBeans\Result\Update($primaryKey, true);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('update')->with($primaryKey, $data)->andReturn($update);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($update, $commonInstance->update($primaryKey, $data));
    }

    public function testUpdateByArray() : void
    {
        $filter = ['active' => 1];
        $data = ['name' => 'Shrek'];
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(1);

        $update = new \CoolBeans\Result\UpdateByArray([$primaryKey], []);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('updateByArray')->with($filter, $data)->andReturn($update);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($update, $commonInstance->updateByArray($filter, $data));
    }

    public function testDelete() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(1);

        $delete = new \CoolBeans\Result\Delete($primaryKey);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('delete')->with($primaryKey)->andReturn($delete);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($delete, $commonInstance->delete($primaryKey));
    }

    public function testDeleteByArray() : void
    {
        $filter = ['active' => 1];
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(1);

        $delete = new \CoolBeans\Result\DeleteByArray([$primaryKey]);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('DeleteByArray')->with($filter)->andReturn($delete);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($delete, $commonInstance->deleteByArray($filter));
    }

    public function testUpsert() : void
    {
        $data = ['name' => 'Shrek'];

        $insert = new \CoolBeans\Result\Insert(new \CoolBeans\PrimaryKey\IntPrimaryKey(1));

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('upsert')->with(null, $data)->andReturn($insert);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($insert, $commonInstance->upsert(null, $data));
    }

    public function testTransaction() : void
    {
        $callable = function() : void {

        };

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('transaction')->with($callable);

        $commonInstance = new class($dataSourceMock) {
            use \CoolBeans\Decorator\TCommon;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        $commonInstance->transaction($callable);
    }
}