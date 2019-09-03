<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2014-06-15T14:17:11+02:00
 * File:          app/code/local/Xtento/OrderExport/Helper/Entity.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Helper_Entity extends Mage_Core_Helper_Abstract
{
    public function getPluralEntityName($entity) {
        return $entity;
    }

    public function getEntityName($entity) {
        $entities = Mage::getModel('xtento_orderexport/export')->getEntities();
        if (isset($entities[$entity])) {
            return rtrim($entities[$entity], 's');
        }
        return ucfirst($entity);
    }
}