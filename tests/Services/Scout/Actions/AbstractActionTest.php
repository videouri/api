<?php

namespace Test\Services\Scout\Actions;

use Videouri\Services\Scout\Actions\AbstractAction;
use Videouri\Services\Scout\Agents\AgentInterface;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Test\Services\Scout\Actions
 */
class AbstractActionTest extends AbstractTestCase
{
    /**
     * @var AbstractAction
     */
    private $classMock;

    /**
     * Before all tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->classMock = $this->getMockForAbstractClass(AbstractAction::class);
    }

    /**
     * @return array
     */
    public function listOfSupportedAgents()
    {
        return [
            ['Youtube'],
            ['Dailymotion'],
            ['Vimeo'],
        ];
    }

    /**
     * @return array
     */
    public function listOfUnsupportedAgents()
    {
        return [
            ['blah'],
            ['halb'],
            ['asfa'],
        ];
    }

    /**
     * @param string $agent
     *
     * @dataProvider listOfSupportedAgents
     */
    public function testLoadingAgentReturnsAgent($agent)
    {
        $agent = $this->classMock->getAgent($agent);
        $this->assertTrue($agent instanceof AgentInterface);
    }

    /**
     * @param string $agent
     *
     * @dataProvider listOfUnsupportedAgents
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp |\w source is not available.|
     */
    public function testLoadingUnsupportedAgentThrowsException($agent)
    {
        $this->classMock->getAgent($agent);
    }
}
