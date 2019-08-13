<?php
/**
 *
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

declare(strict_types=1);

/**
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/.
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Entity\Parts;

use App\Entity\Base\PartsContainingDBElement;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Storelocation.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StructuralDBElementRepository")
 * @ORM\Table("`storelocations`")
 */
class Storelocation extends PartsContainingDBElement
{
    /**
     * @ORM\OneToMany(targetEntity="Storelocation", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Storelocation", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="storelocation")
     */
    protected $parts;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $is_full;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $only_single_part;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $limit_to_existing_parts;

    /**
     * @var MeasurementUnit|null The measurement unit, which parts can be stored in here
     * @ORM\ManyToOne(targetEntity="MeasurementUnit")
     * @ORM\JoinColumn(name="storage_type_id", referencedColumnName="id")
     */
    protected $storage_type;

    /********************************************************************************
     *
     *   Getters
     *
     *********************************************************************************/

    /**
     * Get the "is full" attribute.
     *
     * When this attribute is set, it is not possible to add additional parts or increase the instock of existing parts.
     *
     * @return bool * true if the storelocation is full
     *              * false if the storelocation isn't full
     */
    public function isFull(): bool
    {
        return (bool) $this->is_full;
    }

    /**
     * When this property is set, only one part (but many instock) is allowed to be stored in this store location.
     *
     * @return bool
     */
    public function isOnlySinglePart(): bool
    {
        return $this->only_single_part;
    }

    /**
     * @param bool $only_single_part
     * @return Storelocation
     */
    public function setOnlySinglePart(bool $only_single_part): Storelocation
    {
        $this->only_single_part = $only_single_part;
        return $this;
    }

    /**
     * When this property is set, it is only possible to increase the instock of parts, that are already stored here.
     *
     * @return bool
     */
    public function isLimitToExistingParts(): bool
    {
        return $this->limit_to_existing_parts;
    }

    /**
     * @param bool $limit_to_existing_parts
     * @return Storelocation
     */
    public function setLimitToExistingParts(bool $limit_to_existing_parts): Storelocation
    {
        $this->limit_to_existing_parts = $limit_to_existing_parts;
        return $this;
    }

    /**
     * @return MeasurementUnit|null
     */
    public function getStorageType(): ?MeasurementUnit
    {
        return $this->storage_type;
    }

    /**
     * @param MeasurementUnit|null $storage_type
     * @return Storelocation
     */
    public function setStorageType(?MeasurementUnit $storage_type): Storelocation
    {
        $this->storage_type = $storage_type;
        return $this;
    }



    /********************************************************************************
     *
     *   Setters
     *
     *********************************************************************************/

    /**
     * Change the "is full" attribute of this storelocation.
     *
     *     "is_full" = true means that there is no more space in this storelocation.
     *     This attribute is only for information, it has no effect.
     *
     * @param bool $new_is_full * true means that the storelocation is full
     *                          * false means that the storelocation isn't full
     * @return Storelocation
     */
    public function setIsFull(bool $new_is_full): Storelocation
    {
        $this->is_full = $new_is_full;

        return $this;
    }

    /**
     * Returns the ID as an string, defined by the element class.
     * This should have a form like P000014, for a part with ID 14.
     *
     * @return string The ID as a string;
     */
    public function getIDString(): string
    {
        return 'L'.sprintf('%06d', $this->getID());
    }
}