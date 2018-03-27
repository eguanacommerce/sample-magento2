<?php
/**
 * File: SampleTest.php
 *
 * @author      Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * Github:      https://github.com/maciejslawik
 */

namespace LizardMedia\Sample\Test\Unit\Model;

use LizardMedia\Sample\Api\Data\SampleInterface;
use LizardMedia\Sample\Model\Data\Sample as SampleDataModel;
use LizardMedia\Sample\Model\Data\SampleFactory;
use LizardMedia\Sample\Model\ResourceModel\Sample as SampleResource;
use LizardMedia\Sample\Model\Sample;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class SampleTest
 * @package LizardMedia\Sample\Test\Unit\Model
 */
class SampleTest extends TestCase
{
    private $sampleFactory;

    private $context;

    private $registry;

    private $abstractResource;

    private $abstractDB;

    private $sample;

    private $objectManager;

    protected function setUp()
    {
        $this->sampleFactory = $this->getMockBuilder(SampleFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractResource = $this->getMockBuilder(SampleResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractDB = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->objectManager = new ObjectManager($this);
        $this->sample = $this->objectManager->getObject(
            Sample::class,
            [
                'sampleDataFactory' => $this->sampleFactory,
                'context' => $this->context,
                'registry' => $this->registry,
                'resource' => $this->abstractResource,
                'resourceCollection' => $this->abstractDB,
                'data' => []
            ]
        );
    }

    /**
     * @test
     */
    public function testGetIdentities()
    {
        $id = 1;
        $this->sample->setId($id);

        $expectedIdentity = 'lizardmedia_sample_' . $id;
        $this->assertEquals(
            [$expectedIdentity],
            $this->sample->getIdentities()
        );
    }

    /**
     * @test
     */
    public function testGetDataModel()
    {
        $extensionFactory = $this->getMockBuilder(ExtensionAttributesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $attributeValueFactory = $this->getMockBuilder(AttributeValueFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->objectManager->getObject(
            SampleDataModel::class,
            [
                'extensionFactory' => $extensionFactory,
                'attributeValueFactory' => $attributeValueFactory,
                'data' => []
            ]
        );

        $this->sampleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($dataModel));

        $expectedId = 1;
        $expectedDescription = 'Sample description';
        $expectedTitle = 'Sample title';
        $this->sample->setId($expectedId);
        $this->sample->setDescription($expectedDescription);
        $this->sample->setTitle($expectedTitle);

        $sampleDataModel = $this->sample->getDataModel();

        $this->assertInstanceOf(SampleInterface::class, $sampleDataModel);
        $this->assertEquals($expectedId, $sampleDataModel->getId());
        $this->assertEquals($expectedDescription, $sampleDataModel->getDescription());
        $this->assertEquals($expectedTitle, $sampleDataModel->getTitle());
    }
}
