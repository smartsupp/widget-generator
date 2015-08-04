<?php
namespace Smartsupp;

class ChatGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChatGenerator
     */
    protected $chat;

    protected function setUp()
    {
        parent::setUp();

        $this->chat = new ChatGenerator();
    }

    public function test_javascriptEscape()
    {
        $ret = $this->chat->javascriptEscape('abc123');

        $this->assertEquals('abc123', $ret);
    }

    public function test_javascriptEscape_someScript()
    {
        $ret = $this->chat->javascriptEscape("<script>alert('xss')</script>");

        $this->assertEquals('\x3cscript\x3ealert\x28\x27xss\x27\x29\x3c\x2fscript\x3e', $ret);
    }

    public function test_javascriptEscape_someSpecialChars()
    {
        $ret = $this->chat->javascriptEscape("\"'*!@#$%^&*()_+}{:?><.,/`~");

        $this->assertEquals('\x22\x27\x2a\x21\x40\x23\x24\x25\x5e\x26\x2a\x28\x29\x5f\x2b\x7d\x7b\x3a\x3f\x3e\x3c\x2e' .
            '\x2c\x2f\x60\x7e', $ret);
    }

    public function test_hideWidget()
    {
        $this->chat->hideWidget();

        $this->assertTrue(self::getPrivateField($this->chat, 'hide_widget'));
    }

    public function test_setGoogleAnalytics()
    {
        $this->chat->setGoogleAnalytics('UA-123456789');

        $this->assertEquals('UA-123456789', self::getPrivateField($this->chat, 'ga_key'));
    }

    public function test_setGoogleAnalytics_withOptions()
    {
        $options = array('cookieDomain' => 'foo.example.com');
        $this->chat->setGoogleAnalytics('UA-123456789', $options);

        $this->assertEquals('UA-123456789', self::getPrivateField($this->chat, 'ga_key'));
        $this->assertEquals($options, self::getPrivateField($this->chat, 'ga_options'));
    }

    public function test_widget()
    {
        $this->chat->setWidget('button');
        $this->assertEquals('button', self::getPrivateField($this->chat, 'widget'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Widget value foo is not allowed value. You can use only one of values: button, widget.
     */
    public function test_widget_badParam()
    {
        $this->chat->setWidget('foo');
    }

    public function test_setBoxPosition()
    {
        $this->chat->setBoxPosition('left', 'side', 20, 120);

        $this->assertEquals('left', self::getPrivateField($this->chat, 'align_x'));
        $this->assertEquals('side', self::getPrivateField($this->chat, 'align_y'));
        $this->assertEquals('20', self::getPrivateField($this->chat, 'offset_x'));
        $this->assertEquals('120', self::getPrivateField($this->chat, 'offset_y'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage AllignX value foo is not allowed value. You can use only one of values: right, left.
     */
    public function test_setBoxPosition_badParam()
    {
        $this->chat->setBoxPosition('foo', 'side', 20, 120);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage AllignY value foo is not allowed value. You can use only one of values: side, bottom.
     */
    public function test_setBoxPosition_badParam2()
    {
        $this->chat->setBoxPosition('left', 'foo', 20, 120);
    }

    public function test_addUserExtraInformation()
    {
        $this->chat->addUserExtraInformation('userId', 'User ID', 123);
        $this->chat->addUserExtraInformation('orderedPrice', 'Ordered Price in Eshop', '128 000');

        $this->assertEquals(
            array(
                array('id' => 'userId', 'label' => 'User ID', 'value' => 123),
                array('id' => 'orderedPrice', 'label' => 'Ordered Price in Eshop', 'value' => '128 000')
            ),
            self::getPrivateField($this->chat, 'variables')
        );
    }

    public function test_setUserBasicInformation()
    {
        $this->chat->setUserBasicInformation('Johny Depp', 'johny@depp.com');

        $this->assertEquals('Johny Depp', self::getPrivateField($this->chat, 'name'));
        $this->assertEquals('johny@depp.com', self::getPrivateField($this->chat, 'email'));
    }

    public function test_enableRating()
    {
        $this->chat->enableRating('advanced', true);

        $this->assertTrue(self::getPrivateField($this->chat, 'rating_enabled'));
        $this->assertEquals('advanced', self::getPrivateField($this->chat, 'rating_type'));
        $this->assertTrue(self::getPrivateField($this->chat, 'rating_comment'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Rating type foo is not allowed value. You can use only one of values: advanced, simple.
     */
    public function test_enableRating_badParam()
    {
        $this->chat->enableRating('foo');
    }

    public function test_disableSendEmailTranscript()
    {
        $this->assertTrue(self::getPrivateField($this->chat, 'send_email_transcript'));
        $this->chat->disableSendEmailTranscript();
        $this->assertFalse(self::getPrivateField($this->chat, 'send_email_transcript'));
    }

    public function test_setCookieDomain()
    {
        $this->assertNull(self::getPrivateField($this->chat, 'cookie_domain'));
        $this->chat->setCookieDomain('.foo.bar');
        $this->assertEquals('.foo.bar', self::getPrivateField($this->chat, 'cookie_domain'));
    }

    public function test_setKey()
    {
        $this->assertNull(self::getPrivateField($this->chat, 'key'));
        $this->chat->setKey('123456');
        $this->assertEquals('123456', self::getPrivateField($this->chat, 'key'));
    }

    public function test_setCharset()
    {
        $this->assertEquals('utf-8', self::getPrivateField($this->chat, 'charset'));
        $this->chat->setCharset('utf-32');
        $this->assertEquals('utf-32', self::getPrivateField($this->chat, 'charset'));
    }

    public function test_setLanguage()
    {
        $this->assertEquals('en', self::getPrivateField($this->chat, 'language'));
        $this->chat->setLanguage('cs');
        $this->assertEquals('cs', self::getPrivateField($this->chat, 'language'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Language us is not allowed value. You can use only one of values: en, fr, es, de, ru, cs, sk, pl, hu, cn, da, nl, it, pt, hi, ro, no.
     */
    public function test_setLanguage_badParam()
    {
        $this->chat->setLanguage('us');
    }

    /**
     * Get private / protected field value using \ReflectionProperty object.
     *
     * @static
     * @param mixed $object object to be used
     * @param string $fieldName object property name
     * @return mixed given property value
     */
    public static function getPrivateField($object, $fieldName)
    {
        $refId = new \ReflectionProperty($object, $fieldName);
        $refId->setAccessible(true);
        $value = $refId->getValue($object);
        $refId->setAccessible(false);

        return $value;
    }
}