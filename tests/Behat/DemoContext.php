<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\AppFixtures;
use Behat\Behat\Context\Context;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\PHPMatcher;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class DemoContext extends RestContext
{
    /**
     *
     * @var AppFixtures $fixtures
     */
    private $fixtures;

    /**
     * @var PHPMatcher $matcher
     */
    private $matcher;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(
        Request $request,
        AppFixtures $fixtures,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = new PHPMatcher();
        $this->$entityManager = $this->$entityManager;
    }

    /**
     *  @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();
        // drop and create schema 
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // load fixtures
        $purge = new ORMPurger($this->entityManager);
        $fixturesExecutor = new ORMExecutor($this->entityManager, $purge);

        $fixturesExecutor->execute([$this->fixtures]);

    }
}
