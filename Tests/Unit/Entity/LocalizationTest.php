<?php

namespace Oro\Bundle\LocaleBundle\Tests\Unit\Entity;

use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Tests\Unit\Entity\Stub\Localization;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;

class LocalizationTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new Localization(), [
            ['id', 1],
            ['name', 'test_name'],
            ['languageCode', 'language_test_code'],
            ['formattingCode', 'formatting_test_code'],
            ['parentLocalization', new Localization()],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
        ]);
        $this->assertPropertyCollections(new Localization(), [
            ['childLocalizations', new Localization()],
            ['titles', new LocalizedFallbackValue()],
        ]);
    }

    /**
     * @param bool $expected
     * @param \DateTime $date
     *
     * @dataProvider isUpdatedAtSetDataProvider
     */
    public function testIsUpdatedAtSet($expected, \DateTime $date = null)
    {
        $entity = new Localization();
        $entity->setUpdatedAt($date);

        $this->assertEquals($expected, $entity->isUpdatedAtSet());
    }

    /**
     * @return array
     */
    public function isUpdatedAtSetDataProvider()
    {
        return [
            ['expected' => true, 'date' => new \DateTime()],
            ['expected' => false, 'date' => null],
        ];
    }

    public function testToString()
    {
        $entity = new Localization();
        $expectedString = 'Expected String';
        $entity->setName($expectedString);
        $this->assertEquals($expectedString, (string)$entity);
    }

    public function testConstruct()
    {
        $entity = new Localization();
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $entity->getChildLocalizations());
        $this->assertEmpty($entity->getChildLocalizations()->toArray());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $entity->getTitles());
        $this->assertEmpty($entity->getTitles()->toArray());
    }

    public function testTitleAccessors()
    {
        $entity = $this->getEntity(Localization::class, ['id' => 1]);
        $this->assertEmpty($entity->getTitles()->toArray());

        $defaultTitle = $this->createLocalizedValue('default', true);
        $firstTitle = $this->createLocalizedValue('test1');
        $secondTitleLocalization = $this->getEntity(Localization::class, ['id' => 2]);
        $secondTitle = $this->createLocalizedValue('test2', false, $secondTitleLocalization);

        $parentLocalization = $this->getEntity(Localization::class, ['id' => 3]);

        $localization = $this->getEntity(Localization::class, ['id' => 4]);
        $localization->setParentLocalization($parentLocalization);
        $withParentTitle = $this->createLocalizedValue('testParent', false, $parentLocalization);

        $entity->addTitle($defaultTitle)
            ->addTitle($firstTitle)
            ->addTitle($secondTitle)
            ->addTitle($secondTitle)
            ->addTitle($withParentTitle);

        $this->assertCount(4, $entity->getTitles()->toArray());
        $this->assertEquals(
            [$defaultTitle, $firstTitle, $secondTitle, $withParentTitle],
            array_values($entity->getTitles()->toArray())
        );

        $this->assertEquals($secondTitle, $entity->getTitle($secondTitle->getLocalization()));
        $this->assertEquals($defaultTitle, $entity->getTitle());
        $this->assertEquals($withParentTitle, $entity->getTitle($localization));
        $this->assertEquals($defaultTitle->getString(), $entity->getTitle(new Localization()));

        $entity->removeTitle($firstTitle)->removeTitle($firstTitle)->removeTitle($defaultTitle);

        $this->assertEquals([$secondTitle, $withParentTitle], array_values($entity->getTitles()->toArray()));
    }

    public function testGetDefaultTitle()
    {
        $defaultTitle = $this->createLocalizedValue('default', true);
        $localizedTitle = $this->createLocalizedValue('default');

        $entity = new Localization();
        $entity->addTitle($defaultTitle)
            ->addTitle($localizedTitle);

        $this->assertEquals($defaultTitle, $entity->getDefaultTitle());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage There must be only one default title
     */
    public function testGetDefaultTitleException()
    {
        $titles = [$this->createLocalizedValue('test1', true), $this->createLocalizedValue('test2', true)];
        $entity = new Localization();

        foreach ($titles as $title) {
            $entity->addTitle($title);
        }

        $entity->getDefaultTitle();
    }

    public function testSetDefaultTitle()
    {
        $entity = new Localization();
        $entity->setDefaultTitle('test_title_string1');
        $this->assertEquals('test_title_string1', $entity->getDefaultTitle());

        // check second time to make sure we don't have an exception in getter
        $entity->setDefaultTitle('test_title_string2');
        $this->assertEquals('test_title_string2', $entity->getDefaultTitle());
    }

    /**
     * @param string $value
     * @param bool|false $default
     * @param Localization $localization
     * @return LocalizedFallbackValue
     */
    protected function createLocalizedValue($value, $default = false, Localization $localization = null)
    {
        $localized = (new LocalizedFallbackValue())->setString('some string');

        if (!$default) {
            if (!$localization) {
                $localization = new Localization();
            }
            $localization->setDefaultTitle($value);

            $localized->setLocalization($localization);
        }

        return $localized;
    }
}
