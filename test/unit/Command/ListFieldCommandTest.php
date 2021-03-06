<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\ListFieldCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var ListFieldCommand */
    private $listFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->listFieldCommand = new ListFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->listFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_fields()
    {
        $yml = <<<YML
field:
    name: foo
    handle: bar
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:list-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFields();

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Some field name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some other field name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/And another field name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/andAnotherName/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/TextInput/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/TextArea/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/name:foo/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/handle:bar/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/All installed Fields/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfFields()
    {
        return [
            (new Field())
                ->setId(1)
                ->setHandle('someName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextInput')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('Some field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(2)
                ->setHandle('someOtherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('Some other field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(3)
                ->setHandle('andAnotherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('And another field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
