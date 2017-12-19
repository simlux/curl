<?php declare(strict_types=1);

namespace Simlux\Curl\Tests\Schema;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Simlux\Curl\Exceptions\WrongRangeException;
use Simlux\Curl\Schema\NamingSchema;

class NamingSchemaTest extends TestCase
{
    public function testIncrementSchema()
    {
        $schema = new NamingSchema('page_{INCREMENT}.html', [
            '{INCREMENT}' => NamingSchema::COMPONENT_INCREMENT
        ]);
        $schema->setIncrementStart(1);

        $this->assertSame('page_1.html', $schema->next());
        $this->assertSame('page_2.html', $schema->next());
    }

    public function testIncrementZeroFilledSchema()
    {
        $schema = new NamingSchema('page_{INCREMENT}.html', [
            '{INCREMENT}' => NamingSchema::COMPONENT_INCREMENT_ZEROFILLED
        ]);
        $schema->setIncrementStart(1);
        $schema->setIncrementWidth(3);

        $this->assertSame('page_001.html', $schema->next());
        $this->assertSame('page_002.html', $schema->next());
    }

    public function testThrowingWrongRangeException()
    {
        $schema = new NamingSchema('page_{INCREMENT}.html', [
            '{INCREMENT}' => NamingSchema::COMPONENT_INCREMENT_ZEROFILLED
        ]);
        $schema->setIncrementStart(99);
        $schema->setIncrementWidth(2);

        $this->assertSame('page_99.html', $schema->next());
        $this->expectException(WrongRangeException::class);
        $schema->next();
    }

    public function testDateTimeComponent()
    {
        $schema = new NamingSchema('{DATETIME}_page.html', [
            '{DATETIME}' => NamingSchema::COMPONENT_DATETIME
        ]);

        $schema->setDateTime(Carbon::create(2020, 1, 1, 12, 0, 0));
        $this->assertSame('20200101_120000_page.html', $schema->next());

        $schema->setDateTimeFormat('Y-m-d');
        $this->assertSame('2020-01-01_page.html', $schema->next());

        $schema->setDateTimeFormat('H-i');
        $this->assertSame('12-00_page.html', $schema->next());
    }
}