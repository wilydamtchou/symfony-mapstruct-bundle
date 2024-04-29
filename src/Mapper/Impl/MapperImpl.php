<?php

namespace Willydamtchou\SymfonyMapstruct\Mapper\Impl;

use Willydamtchou\SymfonyUtilities\Collection\Collection;
use Willydamtchou\SymfonyUtilities\Collection\Collection as DtoCollection;
use Willydamtchou\SymfonyUtilities\Collection\Collection as EntityCollection;
use Willydamtchou\SymfonyMapstruct\Exception\MapperException;
use Willydamtchou\SymfonyMapstruct\Mapper\Mapper;
use Willydamtchou\SymfonyUtilities\Model\BaseObject;
use Willydamtchou\SymfonyUtilities\Model\BaseObject as Dto;
use Willydamtchou\SymfonyUtilities\Model\BaseObject as Entity;

class MapperImpl implements Mapper
{
    /**
     * @param string $entityClass
     * @param string $dtoClass
     * @param string $entitiesClass
     * @param string $dtosClass
     * @param bool $forceConversion
     */
    public function __construct(
        protected string $entityClass = Entity::class,
        protected string $dtoClass = Dto::class,
        protected string $entitiesClass = EntityCollection::class,
        protected string $dtosClass = DtoCollection::class,
        protected bool $forceConversion = false
    ) {
    }

    /**
     * @param Dto $dto
     * @param array<string> $convertAttributes
     *
     * @return Entity
     *
     * @throws \ReflectionException
     */
    public function asEntity(Dto $dto, array $convertAttributes = []): Entity
    {
        if (get_class($dto) != $this->dtoClass && !is_subclass_of($this->dtoClass, get_class($dto))) {
            throw new MapperException(get_class($dto), $this->entityClass);
        }

        return $this->convert($dto, $convertAttributes, true);
    }

    /**
     * @param Entity $entity
     * @param array<string> $convertAttributes
     *
     * @return Dto
     *
     * @throws \ReflectionException
     */
    public function asDto(Entity $entity, array $convertAttributes = []): Dto
    {
        if (get_class($entity) != $this->entityClass && !is_subclass_of($this->entityClass, get_class($entity))) {
            throw new MapperException(get_class($entity), $this->entityClass);
        }

        return $this->convert($entity, $convertAttributes);
    }

    /**
     * @param DtoCollection $dtos
     * @param array<string> $convertAttributes
     *
     * @return EntityCollection
     *
     * @throws \ReflectionException
     */
    public function asEntityList(DtoCollection $dtos, array $convertAttributes = []): EntityCollection
    {
        $entities = (new \ReflectionClass($this->entitiesClass))->newInstance();;

        if (
            !($entities instanceof Collection) &&
            get_class($entities) != $this->entitiesClass &&
            !is_subclass_of($this->entitiesClass, get_class($entities))
        ) {
            throw new MapperException(get_class($entities), $this->entitiesClass);
        }

        foreach ($dtos->getAll() as $dto) {
            $entities->add($this->asEntity($dto, $convertAttributes));
        }

        return $entities;
    }

    /**
     * @param EntityCollection $entities
     * @param array<string> $convertAttributes
     *
     * @return DtoCollection
     *
     * @throws \ReflectionException
     */
    public function asDtoList(EntityCollection $entities, array $convertAttributes = []): DtoCollection
    {
        $dtos = (new \ReflectionClass($this->dtosClass))->newInstance();

        if (
            !($dtos instanceof Collection) &&
            get_class($dtos) != $this->dtosClass &&
            !is_subclass_of(get_class($dtos), $this->dtosClass)
        ) {
            throw new MapperException($this->dtosClass, get_class($dtos));
        }

        foreach ($entities->getAll() as $dto) {
            $dtos->add($this->asDto($dto, $convertAttributes));
        }

        return $dtos;
    }

    /**
     * @param BaseObject $object
     * @param array<string> $convertAttributes
     * @param bool $isEntity
     *
     * @return BaseObject
     *
     * @throws \ReflectionException
     */
    public function convert(BaseObject $object, array $convertAttributes, bool $isEntity = false): BaseObject
    {
        if ($this->entityClass == $this->dtoClass && !$this->forceConversion) {
            return $object;
        }

        $reflectionClass = new \ReflectionClass($this->dtoClass);

        if ($isEntity) {
            $reflectionClass = new \ReflectionClass($this->entityClass);
        }

        $attributes = $this->getAttributes($object, $isEntity);

        foreach ($convertAttributes as $source => $target) {
            $attributes[$target] = $attributes[$source];
            unset($attributes[$source]);
        }

        $staticProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_READONLY);

        $result = null;

        if ($staticProperties) {
            $data = [];

            foreach ($staticProperties as $property) {
                $prop = $property->getName();

                if (!array_key_exists($prop, $attributes) || !$attributes[$prop]) {
                    continue;
                }

                $data[] = $attributes[$prop];
            }

            $result = $reflectionClass->newInstance(...$data);
        } else {
            $result = $reflectionClass->newInstance();
            $result->hydrate($attributes);
        }

        if ($isEntity) {
            $result = $this->updateEntityAttributes($result, $object);
        } else {
            $result = $this->updateDtoAttributes($result, $object);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function unsetDtoAttributes(): array {
        return [];
    }

    /**
     * @param Dto $dto
     * @param Entity $entity
     *
     * @return Dto
     */
    public function updateDtoAttributes(Dto $dto, Entity $entity): Dto
    {
        return $dto;
    }

    /**
     * @return array
     */
    public function unsetEntityAttributes(): array {
        return [];
    }

    /**
     * @param Entity $entity
     * @param Dto $dto
     *
     * @return Entity
     */
    public function updateEntityAttributes(Entity $entity, Dto $dto): Entity {
        return $entity;
    }

    /**
     * @param Dto $dto
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getEntityAttributes(Dto $dto): array {
        return $this->getAttributes($dto, true);
    }

    /**
     * @param Entity $entity
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getDtoAttributes(Entity $entity): array {
        return $this->getAttributes($entity);
    }

    /**
     * @param BaseObject $object
     * @param bool $isEntity
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function getAttributes(BaseObject $object, bool $isEntity = false): array {
        $attributes = $object->toArray();

        $unsetAttributes = $this->unsetDtoAttributes();

        if ($isEntity) {
            $unsetAttributes = $this->unsetEntityAttributes();
        }

        foreach ($unsetAttributes as $value) {
            unset($attributes[$value]);
        }

        return $attributes;
    }
}
