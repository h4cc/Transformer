<?php


namespace Enm\TransformerBundle\Tests\NonComplex;

use Enm\Transformer\BaseTransformerTestClass;

class OptionsTest extends BaseTransformerTestClass
{

  public function testRequired()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'required' => true
        ]
      ]
    );

    $this->exceptionWithNullTest($config, 'test');
  }



  public function testRequiredIf()
  {
    $config = array(
      'testA' => [
        'type'    => 'string',
        'options' => [
          'required'               => false,
          'requiredIfNotAvailable' => array(
            'and' => array('testB')
          )
        ]
      ],
      'testB' => [
        'type'    => 'string',
        'options' => [
          'required'               => false,
          'requiredIfNotAvailable' => array(
            'and' => array('testB')
          )
        ]
      ]
    );

    $this->exceptionWithNullTest($config, 'testA');
    $this->exceptionWithNullTest($config, 'testB');
  }



  public function testForbiddenIf()
  {
    $config = array(
      'testA' => [
        'type'    => 'string',
        'options' => [
          'forbiddenIfAvailable' => array('testB')
        ]
      ],
      'testB' => [
        'type'    => 'string',
        'options' => [
          'forbiddenIfAvailable' => array('testA')
        ]
      ],
      'testC' => [
        'type'    => 'string',
        'options' => [
          'forbiddenIfNotAvailable' => array('testA')
        ]
      ],
      'testD' => [
        'type'    => 'string',
        'options' => [
          'forbiddenIfNotAvailable' => array('testB')
        ]
      ]
    );

    try
    {
      $array = array(
        'testB' => 'abc',
        'testD' => 'abc',
      );
      $this->getTransformer()->transform(new \stdClass(), $config, $array);
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
    try
    {
      $array = array(
        'testA' => 'abc',
        'testC' => 'abc',
      );
      $this->getTransformer()->transform(new \stdClass(), $config, $array);
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }

    try
    {
      $array = array(
        'testA' => 'abc',
        'testD' => 'abc',
      );
      $this->getTransformer()->transform(new \stdClass(), $config, $array);
      $this->fail('No Exception thrown with invalid parameters!');
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }

    try
    {
      $array = array(
        'testB' => 'abc',
        'testC' => 'abc',
      );
      $this->getTransformer()->transform(new \stdClass(), $config, $array);
      $this->fail('No Exception thrown with invalid parameters!');
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }

    try
    {
      $array = array(
        'testA' => 'abc',
        'testB' => 'abc',
      );
      $this->getTransformer()->transform(new \stdClass(), $config, $array);
      $this->fail('No Exception thrown with invalid parameters!');
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testMin()
  {
    $config = array(
      'test' => [
        'type'    => 'integer',
        'options' => [
          'min' => 0
        ]
      ]
    );
    $this->expectSuccess($config, 'test', 5);
    $this->exceptionWithNegativeIntegerTest($config, 'test');
  }



  public function testMax()
  {
    $config = array(
      'test' => [
        'type'    => 'integer',
        'options' => [
          'max' => 0
        ]
      ]
    );

    $this->expectSuccess($config, 'test', -5);
    $this->exceptionWithPositiveIntegerTest($config, 'test');
  }



  public function testLength()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'length' => [
            'min' => 2,
            'max' => 20
          ]
        ]
      ]
    );

    $this->expectSuccess($config, 'test', 'Hallo Welt');
    $this->expectFailure($config, 'test', 'avcbegasjguegadsdfbwndisfdshafdsffdsfsdfsfnehajsdsfffdff');
  }



  public function testRegex()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'regex' => '/^[0-9]+$/'
        ]
      ]
    );

    $this->expectSuccess($config, 'test', '123');
    $this->expectFailure($config, 'test', 'avcbegasjguegadsdfbwndisfdshafdsffdsfsdfsfnehajsdsfffdff');
  }



  public function testDefaultValue()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'required'     => true,
          'defaultValue' => 'Hallo Welt!'
        ]
      ]
    );
    try
    {
      $class = $this->getTransformer()->transform(new \stdClass(), $config, array());
      $this->assertEquals('Hallo Welt!', $class->test);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testRename()
  {
    $config = array(
      'test' => [
        'type'     => 'string',
        'renameTo' => 'abc',
        'options'  => [
          'required' => true,
        ]
      ]
    );

    try
    {
      $class = $this->getTransformer()->transform(
        new \stdClass(),
        $config,
        array('test' => 'Hallo Welt!')
      );
      $this->assertEquals('Hallo Welt!', $class->abc);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testStringValidationEmail()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'stringValidation' => 'email',
          'strongValidation' => true,
          'required'         => true,
        ]
      ]
    );
    $this->expectSuccess($config, 'test', 'marien@eosnewmedia.de');
    $this->expectFailure($config, 'test', 'marien@eeoossnneewwmmeeddiiaa.de');
    $this->expectFailure($config, 'test', 'Hallo');
  }



  public function testStringValidationUrl()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'stringValidation' => 'url',
          'required'         => true,
        ]
      ]
    );
    $this->expectSuccess($config, 'test', 'http://eosnewmedia.de');
    $this->expectSuccess($config, 'test', 'https://eosnewmedia.de');
    $this->expectSuccess($config, 'test', 'ftp://eosnewmedia.de');
    $this->expectFailure($config, 'test', 'avcbegasjguegadsdfbwndisfdshafdsffdsfsdfsfnehajsdsfffdff');
  }



  public function testStringValidationIp()
  {
    $config = array(
      'test' => [
        'type'    => 'string',
        'options' => [
          'stringValidation' => 'IP',
          'required'         => true,
        ]
      ]
    );
    $this->expectSuccess($config, 'test', '172.0.0.180');
    $this->expectSuccess($config, 'test', '2001:0db8:85a3:08d3:1319:8a2e:0370:7344');
    $this->expectFailure($config, 'test', '2001:0db8:85a3:08d3:1319:8a2e:0370:7344:4444');
    $this->expectFailure($config, 'test', 'avcbegasjguegadsdfbwndisfdshafdsffdsfsdfsfnehajsdsfffdff');
  }



  public function testRound()
  {
    $config = array(
      'test' => [
        'type'    => 'float',
        'options' => [
          'round' => 2
        ]
      ]
    );
    $this->assertEquals(
      5.23,
      $this->getTransformer()->transform('stdClass', $config, array('test' => 5.234))->test
    );
    $this->exceptionWithNegativeIntegerTest($config, 'test');
  }



  public function testFloor()
  {
    $config = array(
      'test' => [
        'type'    => 'float',
        'options' => [
          'floor' => true
        ]
      ]
    );
    $this->assertEquals(
      5,
      $this->getTransformer()->transform('stdClass', $config, array('test' => 5.234))->test
    );
    $this->exceptionWithNegativeIntegerTest($config, 'test');
  }



  public function testCeil()
  {
    $config = array(
      'test' => [
        'type'    => 'float',
        'options' => [
          'ceil' => true
        ]
      ]
    );
    $this->assertEquals(
      6,
      $this->getTransformer()->transform('stdClass', $config, array('test' => 5.234))->test
    );
    $this->exceptionWithNegativeIntegerTest($config, 'test');
  }
}
