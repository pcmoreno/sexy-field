<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Tardigrades\FieldType;

use ReflectionClass;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FieldTypeGeneratorConfig;

abstract class FieldType implements FieldTypeInterface
{
    /** @var FieldConfig */
    private $fieldConfig;

    /** @var FieldTypeGeneratorConfig */
    private $fieldTypeGeneratorConfig;

    public function __construct(array $fieldTypeGeneratorConfig = [])
    {
        $this->fieldTypeGeneratorConfig = FieldTypeGeneratorConfig::fromArray($fieldTypeGeneratorConfig);
    }

    public function setConfig(FieldConfig $fieldConfig): FieldTypeInterface
    {
        $this->fieldConfig = $fieldConfig;

        return $this;
    }

    public function getFieldTypeGeneratorConfig(): FieldTypeGeneratorConfig
    {
        return $this->fieldTypeGeneratorConfig;
    }

    public function getConfig(): FieldConfig
    {
        return $this->fieldConfig;
    }

    public function formOptions($sectionEntity): array
    {
        $fieldConfig = $this->getConfig()->toArray();
        $options = [];
        if (!empty($fieldConfig['field']['form'])) {
            $entryId = $sectionEntity->getId();
            $options = $fieldConfig['field']['form']['all'];
            if (empty($entryId) && !empty($fieldConfig['field']['form']['create'])) {
                $options = array_merge($options, $fieldConfig['field']['form']['create']);
            }
            if (!empty($entryId) && !empty($fieldConfig['field']['form']['update'])) {
                $options = array_merge($options, $fieldConfig['field']['form']['update']);
            }
        }
        return $options;
    }

    public function hasEntityEvent(string $event): bool
    {
        try {
            $entityEvents = $this->getConfig()->getEntityEvents();
        } catch (\Exception $exception) {
            $entityEvents = [];
        }

        return in_array($event, $entityEvents);
    }

    public function directory(): string
    {
        $fieldType = new ReflectionClass($this);
        return pathinfo($fieldType->getFilename(), PATHINFO_DIRNAME);
    }

    abstract public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection
    ): FormBuilderInterface;
}
