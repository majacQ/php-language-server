<?php
declare(strict_types = 1);

namespace LanguageServer\Tests\Server\TextDocument\Definition;

use LanguageServer\Tests\Server\ServerTestCase;
use LanguageServer\Protocol\{TextDocumentIdentifier, Position, Location, Range};
use function LanguageServer\pathToUri;

class GlobalTest extends ServerTestCase
{
    public function testDefinitionFileBeginning() {
        // |<?php
        $result = $this->textDocument->definition(new TextDocumentIdentifier(pathToUri(realpath(__DIR__ . '/../../../../fixtures/references.php'))), new Position(0, 0));
        $this->assertEquals([], $result);
    }

    public function testDefinitionEmptyResult() {
        // namespace keyword
        $result = $this->textDocument->definition(new TextDocumentIdentifier(pathToUri(realpath(__DIR__ . '/../../../../fixtures/references.php'))), new Position(2, 4));
        $this->assertEquals([], $result);
    }

    public function testDefinitionForClassLike()
    {
        // $obj = new TestClass();
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForClassOnStaticMethodCall()
    {
        // $obj = new TestClass();
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[1];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForClassOnStaticPropertyFetch()
    {
        // echo TestClass::$staticTestProperty;
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[2];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForClassOnConstFetch()
    {
        // TestClass::TEST_CLASS_CONST;
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[3];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForImplements()
    {
        // class TestClass implements TestInterface
        // Get definition for TestInterface
        $reference = $this->getReferenceLocations('TestInterface')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestInterface'), $result);
    }

    public function testDefinitionForClassConstants()
    {
        // echo TestClass::TEST_CLASS_CONST;
        // Get definition for TEST_CLASS_CONST
        $reference = $this->getReferenceLocations('TestClass::TEST_CLASS_CONST')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->end);
        $this->assertEquals($this->getDefinitionLocation('TestClass::TEST_CLASS_CONST'), $result);
    }

    public function testDefinitionForConstants()
    {
        // echo TEST_CONST;
        // Get definition for TEST_CONST
        $reference = $this->getReferenceLocations('TEST_CONST')[1];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TEST_CONST'), $result);
    }

    public function testDefinitionForStaticMethods()
    {
        // TestClass::staticTestMethod();
        // Get definition for staticTestMethod
        $reference = $this->getReferenceLocations('TestClass::staticTestMethod()')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->end);
        $this->assertEquals($this->getDefinitionLocation('TestClass::staticTestMethod()'), $result);
    }

    public function testDefinitionForStaticProperties()
    {
        // echo TestClass::$staticTestProperty;
        // Get definition for staticTestProperty
        $reference = $this->getReferenceLocations('TestClass::staticTestProperty')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->end);
        $this->assertEquals($this->getDefinitionLocation('TestClass::staticTestProperty'), $result);
    }

    public function testDefinitionForMethods()
    {
        // $obj->testMethod();
        // Get definition for testMethod
        $reference = $this->getReferenceLocations('TestClass::testMethod()')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->end);
        $this->assertEquals($this->getDefinitionLocation('TestClass::testMethod()'), $result);
    }

    public function testDefinitionForProperties()
    {
        // echo $obj->testProperty;
        // Get definition for testProperty
        $reference = $this->getReferenceLocations('TestClass::testProperty')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->end);
        $this->assertEquals($this->getDefinitionLocation('TestClass::testProperty'), $result);
    }

    public function testDefinitionForVariables()
    {
        // echo $var;
        // Get definition for $var
        $uri = pathToUri(realpath(__DIR__ . '/../../../../fixtures/references.php'));
        $result = $this->textDocument->definition(new TextDocumentIdentifier($uri), new Position(13, 7));
        $this->assertEquals(new Location($uri, new Range(new Position(12, 0), new Position(12, 10))), $result);
    }

    public function testDefinitionForParamTypeHints()
    {
        // function whatever(TestClass $param) {
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[4];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForReturnTypeHints()
    {
        // function whatever(TestClass $param): TestClass {
        // Get definition for TestClass
        $reference = $this->getReferenceLocations('TestClass')[5];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('TestClass'), $result);
    }

    public function testDefinitionForParams()
    {
        // echo $param;
        // Get definition for $param
        $uri = pathToUri(realpath(__DIR__ . '/../../../../fixtures/references.php'));
        $result = $this->textDocument->definition(new TextDocumentIdentifier($uri), new Position(16, 13));
        $this->assertEquals(new Location($uri, new Range(new Position(15, 18), new Position(15, 34))), $result);
    }

    public function testDefinitionForUsedVariables()
    {
        // echo $var;
        // Get definition for $var
        $uri = pathToUri(realpath(__DIR__ . '/../../../../fixtures/references.php'));
        $result = $this->textDocument->definition(new TextDocumentIdentifier($uri), new Position(20, 11));
        $this->assertEquals(new Location($uri, new Range(new Position(19, 22), new Position(19, 26))), $result);
    }

    public function testDefinitionForFunctions()
    {
        // test_function();
        // Get definition for test_function
        $reference = $this->getReferenceLocations('test_function()')[0];
        $result = $this->textDocument->definition(new TextDocumentIdentifier($reference->uri), $reference->range->start);
        $this->assertEquals($this->getDefinitionLocation('test_function()'), $result);
    }
}
