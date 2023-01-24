<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Integration;

use Symfony\Component\Console\Output\ConsoleOutput;

final class ConsoleOutputSpy extends ConsoleOutput
{
    private string $writeLnContents = '';

    public function writeln($messages, int $options = self::OUTPUT_NORMAL)
    {
        parent::writeln($messages, $options);

        $this->writeLnContents .= $messages;
    }

    public function getWriteLnContents(): string
    {
        return $this->writeLnContents;
    }
}
