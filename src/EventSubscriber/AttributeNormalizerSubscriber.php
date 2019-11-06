<?php
declare(strict_types=1);

namespace Ewave\Bundle\AttributeBundle\EventSubscriber;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Ewave\Bundle\AttributeBundle\Property\PropertyConfig;
use Ewave\Bundle\AttributeBundle\Property\Provider\PropertyConfigProvider;
use Ewave\Bundle\CustomEventsBundle\Event\NormalizerEvent;
use Ewave\Bundle\CustomEventsBundle\StorageUtils\StorageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AttributeNormalizerSubscriber
 *
 * @package Ewave\Bundle\AttributeBundle\EventSubscriber
 */
class AttributeNormalizerSubscriber implements EventSubscriberInterface
{
    /**
     * @var PropertyConfigProvider
     */
    protected $propertyConfigProvider;

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
            StorageEvents::ENTITY_NORMALIZE_AFTER => 'addNormalizedProperties',
        ];
    }

    /**
     * @param NormalizerEvent $event
     *
     * @return $this
     */
    public function addNormalizedProperties(NormalizerEvent $event)
    {
        if (!$this->supports($event)) {
            return $this;
        }

        /** @var AttributeInterface $attribute */
        $attribute = $event->getSourceData();
        $normalizedData = $event->getNormalizedData();

        /**
         * @var PropertyConfig $propertyConfig
         */
        $propertyConfigs = $this->propertyConfigProvider->getAttributePropertyConfigs($attribute);
        foreach ($propertyConfigs as $propertyCode => $propertyConfig) {
            $normalizedData[$propertyCode] = $attribute->getProperty($propertyCode);
        }

        $event->setNormalizedData($normalizedData);

        return $this;
    }

    /**
     * @param NormalizerEvent $event
     *
     * @return bool
     */
    protected function supports(NormalizerEvent $event)
    {
        return $event->getSourceData() instanceof AttributeInterface
            && ('standard' === $event->getFormat() || '' === $event->getFormat())
            && is_array($event->getNormalizedData())
            && $this->propertyConfigProvider->getAttributePropertyConfigs($event->getSourceData());
    }
}
