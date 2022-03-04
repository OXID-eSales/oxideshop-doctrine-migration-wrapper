<?php

namespace OxidEsales\DoctrineMigrationWrapper;

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
        if (isset($edition) && substr($edition, 0, 1) === '-') {
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

            foreach ($copyOfArgv as $flag) {
                /*
                 * Determines if a param has also a value
                 * if case  : --write-sql=/var/www/html/source/migration/project_data/
                 * else case: --dry-run
                 */
                $keyValuePair = preg_split('/=/', $flag);

                if (count($keyValuePair) === 2) {
                    $flags[$keyValuePair[0]] = $keyValuePair[1];
                } else {
                    $flags[$flag] = null;
                }
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
}