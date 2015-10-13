<?php

use LaravelPlus\Extension\Addons\Console\AddonCheckCommand as Command;

class AddonCheckCommandTest extends TestCase
{
    use ConsoleCommandTrait;

    /**
     * @test
     */
    public function test_withNoParameter()
    {
        // 1. setup
        $app = $this->createApplication();

        // 2. condition

        // 3. test
        $command = new Command();

        try {
            $result = $this->runCommand($app, $command);

            Assert::same(0, $result);
        } catch (RuntimeException $ex) {
            Assert::failed($ex->getMessage());
        }
    }
}