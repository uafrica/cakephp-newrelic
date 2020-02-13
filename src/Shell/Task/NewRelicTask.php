<?php
declare(strict_types=1);

namespace NewRelic\Shell\Task;

use App\Shell\AppShell;
use NewRelic\Traits\NewRelicTrait;

class NewRelicTask extends AppShell
{
    use NewRelicTrait;
}
