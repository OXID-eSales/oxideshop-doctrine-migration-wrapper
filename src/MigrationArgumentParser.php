<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use function str_starts_with;

class MigrationArgumentParser
{
    private ?string $command;
    private ?string $edition;
    private array $flags;

    public function __construct(array $commandLineArguments)
    {
        $this->parse($commandLineArguments);
    }

    protected function parse(array $argv)
    {
        $this->command = $argv[1] ?? null;

        $edition = $argv[2] ?? null;
        // Just in case the second argument is a flag and edition is not set
        if (isset($edition) && str_starts_with($edition, '-')) {
            array_splice($argv, 3, 0, $edition);
            $argv[2] = null;
            $edition = null;
        }
        $this->edition = $edition;

        $flags = [];
        if (isset($argv[3])) {
            // Do not alter $argv itself
            $copyOfArgv = $argv;

            unset(
                $copyOfArgv[0],
                $copyOfArgv[1],
                $copyOfArgv[2]
            );
            $versions = [];
            foreach ($copyOfArgv as $flag) {
                if ($this->isVersionArgument((string)$flag)) {
                    $versions[] = $flag;
                    continue;
                }
                /*
                 * Determines if a param has also a value
                 * if case  : --write-sql=/var/www/html/source/migration/project_data/
                 * else case: --dry-run
                 */
                $keyValuePair = explode('=', $flag);

                if (count($keyValuePair) === 2) {
                    $flags[$keyValuePair[0]] = $keyValuePair[1];
                } else {
                    $flags[$flag] = null;
                }
            }
            if ($versions) {
                $flags['versions'] = $versions;
            }
        }
        $this->flags = $flags;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    private function isVersionArgument(string $flag): bool
    {
        return !str_starts_with($flag, '-');
    }
}
