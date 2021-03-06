<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Core\Utils;

class ClassFinder
{
    protected $classParser;

    protected $pathsTemplate = [
        'corePath' => 'application/Espo/{category}',
        'modulePath' => 'application/Espo/Modules/{*}/{category}',
        'customPath' => 'custom/Espo/Custom/{category}',
    ];

    protected $dataHash = [];

    public function __construct(File\ClassParser $classParser)
    {
        $this->classParser = $classParser;
    }

    /**
     * Find class name by category (e.g. Controllers, Services) and name.
     */
    public function find(string $category, string $name) : ?string
    {
        $map = $this->getMap($category);
        $className = $map[$name] ?? null;
        return $className;
    }

    /**
     * Get [name => class-name] map.
     */
    public function getMap(string $category) : array
    {
        if (!array_key_exists($category, $this->dataHash)) {
            $this->load($category);
        }
        return $this->dataHash[$category] ?? [];
    }

    protected function load(string $category)
    {
        $path = $this->buildPaths($category);
        $cacheFile = $this->buildCacheFilePath($category);
        $this->dataHash[$category] = $this->classParser->getData($path, $cacheFile);
    }

    protected function buildPaths(string $category) : array
    {
        $paths = [];
        foreach ($this->pathsTemplate as $key => $value) {
            $path[$key] = str_replace('{category}', $category, $value);
        }
        return $path;
    }

    protected function buildCacheFilePath(string $category) : string
    {
        $path = 'data/cache/application/classmap_' . str_replace('/', '_', strtolower($category)) . '.php';
        return $path;
    }
}
