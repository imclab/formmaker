<?php

namespace MakingWaves\FormMakerBundle\eZ\Publish\FieldType\Form;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;

class Type extends FieldType
{
    private $typeIdentifier = 'form';

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $doctrine;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $container = \ezpKernel::instance()->getServiceContainer();
        $this->doctrine = $container->get('doctrine');
        $this->translator = $container->get('translator');
    }
    
    /**
     * Getter for type identifier
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return $this->typeIdentifier;
    }

    /**
     * @param mixed $inputValue
     * @return Value|mixed
     */
    protected function createValueFromInput( $inputValue )
    {
        $valueInstance = new Value(array(
            'formId' => $inputValue
        ));
        
        return $valueInstance;
    }

    /**
     * Validate the input
     * @param \eZ\Publish\Core\FieldType\Value $value
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    protected function checkValueStructure(\eZ\Publish\Core\FieldType\Value $value)
    {
        if(!is_numeric($value->formId)) {
            
            throw new \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType(
                '$value->formId',
                'integer',
                $value->formId
            );
        }
    }

    /**
     * @return \eZ\Publish\SPI\FieldType\Value|Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        return array();
    }

    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        return array();
    }

    public function getName(SPIValue $value)
    {
        return 'some name';
    }

    protected function getSortInfo(CoreValue $value)
    {
        return $this->getName($value);
    }

    public function fromHash($hash)
    {
        if ($hash === null)
        {
            return $this->getEmptyValue();
        }

        return new Value( $hash );
    }

    public function toHash(SPIValue $value)
    {
        if ( $this->isEmptyValue($value))
        {
            return null;
        }
        return array(
            'formId' => $value->formId
        );
    }

    /**
     * @param SPIValue $value
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function toPersistenceValue( SPIValue $value )
    {
        if ( $value === null )
        {
            return new PersistenceValue(
                array(
                    "data" => null,
                    "externalData" => null,
                    "sortKey" => null,
                )
            );
        }
        return new PersistenceValue(
            array(
                "data" => $this->toHash( $value ),
                "sortKey" => $this->getSortInfo( $value ),
            )
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     * @return \EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Form\Value
     */
    public function fromPersistenceValue( PersistenceValue $fieldValue )
    {
        if ( $fieldValue->data === null )
        {
            return $this->getEmptyValue();
        }

        return new Value( $fieldValue->data );
    }
}
