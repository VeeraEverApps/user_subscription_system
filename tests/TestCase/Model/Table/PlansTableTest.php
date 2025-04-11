<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlansTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlansTable Test Case
 */
class PlansTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlansTable
     */
    protected $Plans;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Plans',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Plans') ? [] : ['className' => PlansTable::class];
        $this->Plans = $this->getTableLocator()->get('Plans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Plans);

        parent::tearDown();
    }
}
