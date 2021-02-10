<?php

namespace tests\phpunit\FrontendBuild;

use Aws\MockHandler;
use Aws\Result;
use Aws\Ssm\SsmClient;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests of the Jenkins client.
 *
 * @coversDefaultClass \Drupal\va_gov_build_trigger\Service\SystemsManagerClient
 */
class SystemsManagerClientTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'datetime',
    'va_gov_build_trigger',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $mock = new MockHandler();
    $mock->append(new Result([
      'Parameter' => [
        'ARN' => '<string>',
        'DataType' => '<string>',
        'LastModifiedDate' => 'some date',
        'Name' => '<string>',
        'Selector' => '<string>',
        'SourceResult' => '<string>',
        'Type' => 'String|StringList|SecureString',
        'Value' => 'THIS IS MY IMAGINARY JENKINS API TOKEN',
        'Version' => 123,
      ],
    ]));
    $ssmClient = new SsmClient([
      'version' => 'latest',
      'region' => 'us-gov-west-1',
      'handler' => $mock,
      'credentials' => [
        'key'    => 'FAKE AWS ACCESS KEY',
        'secret' => 'FAKE AWS SECRET KEY',
      ],
    ]);
    $this->container->set('va_gov_build_trigger.aws_ssm_client', $ssmClient);
  }

  /**
   * Test that the systems manager correctly requests the token from AWS.
   *
   * @covers ::getJenkinsApiToken
   */
  public function testGetJenkinsApiToken() {
    $systemsManagerClient = $this->container->get('va_gov_build_trigger.systems_manager_client');
    $token = $systemsManagerClient->getJenkinsApiToken();
    $this->assertEquals('THIS IS MY IMAGINARY JENKINS API TOKEN', $token);
  }

}
