<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\EventSubscriber;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Ewave\Bundle\AttributeBundle\Property\PropertyConfig;
use Ewave\Bundle\AttributeBundle\Property\Provider\PropertyConfigProvider;
use Ewave\Bundle\CustomEventsBundle\StorageUtils\StorageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class AttributePropertiesUpdaterSubscriber
 *
 * @package Ewave\Bundle\AttributeBundle\EventSubscriber
 */
class AttributePropertiesUpdaterSubscriber implements EventSubscriberInterface
{
    /**
     * @var PropertyConfigProvider
     */
    private $propertyConfigProvider;

    /**
     * AttributePropertiesUpdaterSubscriber constructor.
     *
     * @param PropertyConfigProvider $propertyConfigProvider
     */
    public function __construct(
        PropertyConfigProvider $propertyConfigProvider
    ) {
        $this->propertyConfigProvider = $propertyConfigProvider;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            StorageEvents::ENTITY_UPDATE_BEFORE => 'setAttributeProperties',
        ];
    }

    /**
     * @param GenericEvent $event
     *
     * @return $this
     */
    public function setAttributeProperties(GenericEvent $event)
    {
        $attribute = $event->getSubject();
        if (!$attribute instanceof AttributeInterface) {
            return $this;
        }

        $attributeData = $event->getArgument('data');
        $newAttribute = false;
        //complete attribute type to get required configs and clear the type back for regular processing
        if (null === $attribute->getType()) {
            $type = $attributeData['type'] ?? null;
            $attribute->setType($type);
            $newAttribute = true;
        }

        if (!$propertyConfigs = $this->propertyConfigProvider->getAttributePropertyConfigs($attribute)) {
            return $this;
        }

        /**
         * @var PropertyConfig $propertyConfig
         */
        foreach ($propertyConfigs as $propertyCode => $propertyConfig) {
            $propertyValue = $attribute->getProperty($propertyCode);
            if (array_key_exists($propertyCode, $attributeData)) {
                $propertyValue = $attributeData[$propertyCode];
                unset($attributeData[$propertyCode]);
            } elseif (null === $propertyValue && null !== $propertyConfig->getDefaultValue()) {
                $propertyValue = $propertyConfig->getDefaultValue();
            }

            $attribute->setProperty($propertyCode, $propertyValue);
        }

        if ($newAttribute) {
            $attribute->setType(null);
        }

        $event->setArgument('data', $attributeData);

        return $this;
    }
}
