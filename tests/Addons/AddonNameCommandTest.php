<?php

use LaravelPlus\Extension\Addons\Console\AddonNameCommand as Command;

class AddonNameCommandTest extends TestCase
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

            Assert::isFalse(true);
        } catch (RuntimeException $ex) {
            Assert::equals('Not enough arguments.', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function test_withAddonParameter()
    {
        // 1. setup
        $app = $this->createApplication();

        // 2. condition

        // 3. test
        $command = new Command();

        try {
            $result = $this->runCommand($app, $command, [
                'addon' => 'foo',
            ]);

            Assert::isFalse(true);
        } catch (RuntimeException $ex) {
            Assert::equals('Not enough arguments.', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function test_withAddonAndNamespaceParameter_addonNotFound()
    {
        // 1. setup
        $app = $this->createApplication();

        // 2. condition

        // 3. test
        $command = new Command();

        try {
            $result = $this->runCommand($app, $command, [
                'addon' => 'foo',
                'namespace' => 'bar',
            ]);

            Assert::isFalse(true);
        } catch (RuntimeException $ex) {
            Assert::equals("Addon 'foo' is not found.", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function test_withAddonAndNamespaceParameter_addonFound()
    {
        // 1. setup
        $app = $this->createApplication();
        $this->createAddon('foo', 'minimum', [
            'namespace' => 'Foo',
        ]);

        // 2. condition

        // 3. test
        $command = new Command();

        try {
            $result = $this->runCommand($app, $command, [
                'addon' => 'foo',
                'namespace' => 'bar',
            ]);

            Assert::same(0, $result);
        } catch (RuntimeException $ex) {
            Assert::failed($ex->getMessage());
        }
    }
}
