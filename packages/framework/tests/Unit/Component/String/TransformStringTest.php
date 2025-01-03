<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\String;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\String\TransformString;

class TransformStringTest extends TestCase
{
    public static function safeFilenameProvider()
    {
        return [
            [
                'actual' => 'ěščřžýáíé.dat',
                'expected' => 'escrzyaie.dat',
            ],
            [
                'actual' => 'ĚŠČŘŽÝÁÍÉ.DAT',
                'expected' => 'ESCRZYAIE.DAT',
            ],
            [
                'actual' => 'Foo     Bar.dat',
                'expected' => 'Foo_Bar.dat',
            ],
            [
                'actual' => 'Foo-Bar.dat',
                'expected' => 'Foo-Bar.dat',
            ],
            [
                'actual' => '../../Foo.dat',
                'expected' => '_._Foo.dat',
            ],
            [
                'actual' => '..\\..\\Foo.dat',
                'expected' => '_._Foo.dat',
            ],
            [
                'actual' => '.foo.dat',
                'expected' => 'foo.dat',
            ],
            [
                'actual' => 'BG 747 fixˇ.dat',
                'expected' => 'BG_747_fix.dat',
            ],
        ];
    }

    /**
     * @param mixed $actual
     * @param mixed $expected
     */
    #[DataProvider('safeFilenameProvider')]
    public function testSafeFilename($actual, $expected)
    {
        $this->assertSame($expected, TransformString::safeFilename($actual));
    }

    public static function stringToFriendlyUrlSlugProvider()
    {
        return [
            [
                'actual' => 'ěščřžýáíé foo',
                'expected' => 'escrzyaie-foo',
            ],
            [
                'actual' => 'ĚŠČŘŽÝÁÍÉ   ',
                'expected' => 'escrzyaie',
            ],
            [
                'actual' => 'Foo     Bar-Baz',
                'expected' => 'foo-bar-baz',
            ],
            [
                'actual' => 'foo-bar_baz',
                'expected' => 'foo-bar_baz',
            ],
            [
                'actual' => '$€@!?<>=;~%^&',
                'expected' => '',
            ],
            [
                'actual' => 'Příliš žluťoučký kůň úpěl ďábelské ódy',
                'expected' => 'prilis-zlutoucky-kun-upel-dabelske-ody',
            ],
            [
                'actual' => 'BG-747 is fixedˇ',
                'expected' => 'bg-747-is-fixed',
            ],
        ];
    }

    /**
     * @param mixed $actual
     * @param mixed $expected
     */
    #[DataProvider('stringToFriendlyUrlSlugProvider')]
    public function testStringToFriendlyUrlSlug($actual, $expected)
    {
        $this->assertSame($expected, TransformString::stringToFriendlyUrlSlug($actual));
    }

    public static function stringToCamelCaseProvider()
    {
        return [
            [
                'actual' => 'ěščřžýáíé foo',
                'expected' => 'escrzyaieFoo',
            ],
            [
                'actual' => 'ĚŠČŘŽÝÁÍÉ   ',
                'expected' => 'escrzyaie',
            ],
            [
                'actual' => 'Foo     Bar-Baz',
                'expected' => 'fooBarBaz',
            ],
            [
                'actual' => 'foo-bar_baz',
                'expected' => 'fooBarBaz',
            ],
            [
                'actual' => '$€@!?<>=;~%^&',
                'expected' => '',
            ],
            [
                'actual' => 'Příliš žluťoučký kůň úpěl ďábelské ódy',
                'expected' => 'prilisZlutouckyKunUpelDabelskeOdy',
            ],
            [
                'actual' => 'BG-747 is fixedˇ',
                'expected' => 'bg747IsFixed',
            ],
            [
                'actual' => 'camelCase-camelCase',
                'expected' => 'camelCaseCamelCase',
            ],
            [
                'actual' => 'camelCaseACRONYM ACRONYM',
                'expected' => 'camelCaseAcronymAcronym',
            ],
        ];
    }

    /**
     * @param mixed $actual
     * @param mixed $expected
     */
    #[DataProvider('stringToCamelCaseProvider')]
    public function testStringToCamelCase($actual, $expected)
    {
        $this->assertSame($expected, TransformString::stringToCamelCase($actual));
    }

    /**
     * @return array
     */
    public static function stringTrailingSlashesProvider(): array
    {
        return [
            [
                'foo',
                'foo/',
            ],
            [
                'foo/bar',
                'foo/bar/',
            ],
            [
                'foo/',
                'foo',
            ],
            [
                'foo/bar/',
                'foo/bar',
            ],
            [
                '',
                '/',
            ],
            [
                '/',
                '',
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expected
     */
    #[DataProvider('stringTrailingSlashesProvider')]
    public function testAddOrRemoveTrailingSlashFromString(string $string, string $expected): void
    {
        static::assertSame($expected, TransformString::addOrRemoveTrailingSlashFromString($string));
    }

    /**
     * @param string|null $original
     * @param string|null $expected
     */
    #[DataProvider('trimmedStringOrNullProvider')]
    public function testGetTrimmedStringOrNullOnEmpty(?string $original, ?string $expected): void
    {
        static::assertSame($expected, TransformString::getTrimmedStringOrNullOnEmpty($original));
    }

    /**
     * @return array
     */
    public static function trimmedStringOrNullProvider(): array
    {
        return [
            [
                'foo ',
                'foo',
            ],
            [
                'foo  ',
                'foo',
            ],
            [
                "\t  foo",
                'foo',
            ],
            [
                "foo\n\t",
                'foo',
            ],
            [
                '',
                null,
            ],
            [
                '  ',
                null,
            ],
        ];
    }

    /**
     * @param string|null $htmlString
     * @param string|null $expected
     */
    #[DataProvider('convertHtmlToPlainTextDataProvider')]
    public function testConvertHtmlToPlainText(?string $htmlString, ?string $expected): void
    {
        $this->assertSame($expected, TransformString::convertHtmlToPlainText($htmlString));
    }

    /**
     *  @return iterable
     */
    public static function convertHtmlToPlainTextDataProvider(): iterable
    {
        yield 'null' => [
            'htmlString' => null,
            'expected' => null,
        ];

        yield 'empty string' => [
            'htmlString' => '',
            'expected' => '',
        ];

        yield 'string without html tags' => [
            'htmlString' => 'foo bar',
            'expected' => 'foo bar',
        ];

        yield 'string with html tags' => [
            'htmlString' => '<p>foo <strong>bar</strong></p>',
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new line' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p>",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new line and trailing space' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> ",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new line and space and tab' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> \t",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new line and tab and multiple spaces' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> \t  ",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new lines and spaces and tab' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> \t  \n",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new lines and spaces and tab and trailing space' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> \t  \n ",
            'expected' => 'foo bar',
        ];

        yield 'string with html tags and new lines and spaces and tabs' => [
            'htmlString' => "<p>foo\n<strong>bar</strong></p> \t  \n \t",
            'expected' => 'foo bar',
        ];

        yield 'string with html entities' => [
            'htmlString' => '&#34;foo &amp; bar&#34;',
            'expected' => '"foo & bar"',
        ];
    }
}
