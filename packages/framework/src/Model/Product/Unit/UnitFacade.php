<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UnitFacade
{
    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitRepository $unitRepository
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFactoryInterface $unitFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly UnitRepository $unitRepository,
        protected readonly Setting $setting,
        protected readonly UnitFactoryInterface $unitFactory,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param int $unitId
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function getById($unitId)
    {
        return $this->unitRepository->getById($unitId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function create(UnitData $unitData)
    {
        $unit = $this->unitFactory->create($unitData);
        $this->em->persist($unit);
        $this->em->flush();

        $this->dispatchUnitEvent($unit, UnitEvent::CREATE);

        return $unit;
    }

    /**
     * @param int $unitId
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function edit($unitId, UnitData $unitData)
    {
        $unit = $this->unitRepository->getById($unitId);
        $unit->edit($unitData);
        $this->em->flush();

        $this->dispatchUnitEvent($unit, UnitEvent::UPDATE);

        return $unit;
    }

    /**
     * @param int $unitId
     * @param int|null $newUnitId
     */
    public function deleteById($unitId, $newUnitId = null)
    {
        $oldUnit = $this->unitRepository->getById($unitId);

        // intentionally called before unit ids in product are changed
        $this->dispatchUnitEvent($oldUnit, UnitEvent::DELETE);

        if ($newUnitId !== null) {
            $newUnit = $this->unitRepository->getById($newUnitId);
            $this->unitRepository->replaceUnit($oldUnit, $newUnit);

            if ($this->isUnitDefault($oldUnit)) {
                $this->setDefaultUnit($newUnit);
            }
        }

        $this->em->remove($oldUnit);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit[]
     */
    public function getAll()
    {
        return $this->unitRepository->getAll();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return bool
     */
    public function isUnitUsed(Unit $unit)
    {
        return $this->unitRepository->existsProductWithUnit($unit);
    }

    /**
     * @return bool
     */
    public function isAtLeastOneUnitCreated(): bool
    {
        return $this->unitRepository->isAtLeastOneUnitCreated();
    }

    /**
     * @param int $unitId
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit[]
     */
    public function getAllExceptId($unitId)
    {
        return $this->unitRepository->getAllExceptId($unitId);
    }

    /**
     * @return int
     */
    protected function getDefaultUnitId()
    {
        return $this->setting->get(Setting::DEFAULT_UNIT);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function getDefaultUnit()
    {
        $defaultUnitId = $this->getDefaultUnitId();

        return $this->unitRepository->getById($defaultUnitId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     */
    public function setDefaultUnit(Unit $unit)
    {
        $this->setting->set(Setting::DEFAULT_UNIT, $unit->getId());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @return bool
     */
    public function isUnitDefault(Unit $unit)
    {
        return $this->getDefaultUnit() === $unit;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\Unit $unit
     * @param string $eventType
     * @see \Shopsys\FrameworkBundle\Model\Product\Unit\UnitEvent class
     */
    protected function dispatchUnitEvent(Unit $unit, string $eventType): void
    {
        $this->eventDispatcher->dispatch(new UnitEvent($unit), $eventType);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->unitRepository->getCount();
    }
}
