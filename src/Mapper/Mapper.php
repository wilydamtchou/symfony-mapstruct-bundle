<?php

namespace Willydamtchou\SymfonyMapstruct\Mapper;

use Willydamtchou\SymfonyUtilities\Collection\Collection as DtoCollection;
use Willydamtchou\SymfonyUtilities\Collection\Collection as EntityCollection;
use Willydamtchou\SymfonyUtilities\Model\BaseObject;
use Willydamtchou\SymfonyUtilities\Model\BaseObject as Dto;
use Willydamtchou\SymfonyUtilities\Model\BaseObject as Entity;

interface Mapper
{
    /**
     * @param Dto $dto
     * @param array<string> $convertAttributes
     *
     * @return Entity
     */
    public function asEntity(Dto $dto, array $convertAttributes = []): Entity;

    /**
     * @param Entity $entity
     * @param array<string> $convertAttributes
     *
     * @return Dto
     */
    public function asDto(Entity $entity, array $convertAttributes = []): Dto;

    /**
     * @param DtoCollection $dtos
     * @param array<string> $convertAttributes
     *
     * @return EntityCollection
     */
    public function asEntityList(DtoCollection $dtos, array $convertAttributes = []): EntityCollection;

    /**
     * @param EntityCollection $entities
     * @param array<string> $convertAttributes
     *
     * @return DtoCollection
     */
    public function asDtoList(EntityCollection $entities, array $convertAttributes = []): DtoCollection;

    /**
     * @param BaseObject $object
     * @param array<string> $convertAttributes
     * @param bool $isEntity
     *
     * @return BaseObject
     */
    public function convert(BaseObject $object, array $convertAttributes, bool $isEntity = false): BaseObject;

    /**
     * @return array
     */
    public function unsetDtoAttributes(): array;

    /**
     * @return array
     */
    public function unsetEntityAttributes(): array;

    /**
     * @param Dto $dto
     * @param Entity $entity
     *
     * @return Dto
     */
    public function updateDtoAttributes(Dto $dto, Entity $entity): Dto;

    /**
     * @param Entity $entity
     * @param Dto $dto
     *
     * @return Entity
     */
    public function updateEntityAttributes(Entity $entity, Dto $dto): Entity;

    /**
     * @param Dto $dto
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getEntityAttributes(Dto $dto): array;

    /**
     * @param Entity $entity
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getDtoAttributes(Entity $entity): array;
}
