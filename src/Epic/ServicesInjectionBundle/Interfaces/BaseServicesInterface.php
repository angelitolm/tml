<?php

namespace Epic\ServicesInjectionBundle\Interfaces;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityRepository;

interface BaseServicesInterface
{
    /**
     * @return EntityRepository
     */
    public function getRepository();

    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @return string
     */
    public function getEntityShortcutName();

    /**
     * @param null|integer id
     * @return Entity
     */
    public function getEntity($id = null);
} 