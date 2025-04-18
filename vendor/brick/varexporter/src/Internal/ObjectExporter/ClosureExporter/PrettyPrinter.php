<?php

declare(strict_types=1);

namespace Brick\VarExporter\Internal\ObjectExporter\ClosureExporter;

use Override;
use PhpParser\PrettyPrinter\Standard;

/**
 * Extends the standard pretty-printer to allow for a base indent level.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PrettyPrinter extends Standard
{
    private int $varExporterNestingLevel = 0;

    public function setVarExporterNestingLevel(int $level) : void
    {
        $this->varExporterNestingLevel = $level;
    }

    #[Override]
    protected function resetState() : void
    {
        parent::resetState();

        $this->indentLevel = 4 * $this->varExporterNestingLevel;
        $this->nl = "\n" . str_repeat(' ', $this->indentLevel);
    }
}
