<?php

namespace Tests\Formats;

use Done\Subtitles\Code\Converters\CsvConverter;
use Done\Subtitles\Code\Helpers;
use Done\Subtitles\Subtitles;
use PHPUnit\Framework\TestCase;
use Helpers\AdditionalAssertionsTrait;

class CsvTest extends TestCase {

    use AdditionalAssertionsTrait;

    public function testRecognizesCsvFormat()
    {
        $csv = 'Start,End,Text
137.44,140.375,"Senator, we\'re making our final approach into Coruscant."
3740.476,3742.501,"Very good, Lieutenant."';
        $converter = Helpers::getConverterByFileContent($csv);
        $this->assertTrue($converter::class === CsvConverter::class, $converter::class);
    }

    public function testFileToInternalFormat()
    {
        $csv = 'Start,End,Text
137.44,140.375,"Senator, we\'re making our final approach into Coruscant."
3740.476,3742.501,"Very good, Lieutenant."';
        $actual_internal_format = Subtitles::loadFromString($csv)->getInternalFormat();
        $expected_internal_format = (new Subtitles())
        ->add(137.44, 140.375, ['Senator, we\'re making our final approach into Coruscant.'])
        ->add(3740.476, 3742.501, ['Very good, Lieutenant.'])->getInternalFormat();

        $this->assertInternalFormatsEqual($expected_internal_format, $actual_internal_format);
    }

    public function testConvertToFile()
    {
        $actual_csv_string = (new Subtitles())
        ->add(137.44, 140.375, ['Senator, we\'re making', 'our final approach into Coruscant.'])
        ->add(3740.476, 3742.501, ['Very good, Lieutenant.'])->content('csv');
        $expected_csv_string = 'Start,End,Text
137.44,140.375,"Senator, we\'re making our final approach into Coruscant."
3740.476,3742.501,"Very good, Lieutenant."
';
        $expected_csv_string = str_replace("\r", "", $expected_csv_string);

        $this->assertEquals($expected_csv_string, $actual_csv_string);
    }

    public function testClientAnsiFile()
    {
        $actual = Subtitles::loadFromFile('./tests/files/csv_ansi.csv')->getInternalFormat();
        $expected = (new Subtitles())
            ->add(1, 2, 'Oh! Can I believe my eyes!')
            ->add(2, 3, ['If Heaven and earth,', 'if mortals and angels'])
            ->getInternalFormat();
        $this->assertInternalFormatsEqual($expected, $actual);
    }
}