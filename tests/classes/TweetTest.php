<?php 

require_once '../../classes/Tweet.php';

class TweetTest extends PHPUnit_Framework_TestCase
{

    public function testNumericId()
    {
        $tweet = new Tweet();
        $tweet->setId(123);

        $this->assertEquals(123, $tweet->getId());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetIdNumericException()
    {
        $tweet = new Tweet();
        $tweet->setId('ABC');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetIdNumericException()
    {
        $tweet = new myClass();
        $tweet->setId('ABC');
        $tweet->getId();
    }

    public function testTextString()
    {
        $tweet = new Tweet();
        $tweet->setText('Hello world!');

        $this->assertEquals('Hello world!', $tweet->getText());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTextException()
    {
        $tweet = new Tweet();
        $tweet->setText(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetTextNumericException()
    {
        $tweet = new myClass();
        $tweet->setText(123);
        $tweet->getText();
    }

    public function testExchangeArraySuccess()
    {
        $data = new stdClass();
        $data->id = 123;
        $data->text = 'RT: Something';

        $tweet = new Tweet();

        $result = $tweet->exchangeArray($data);

        $this->assertInstanceOf('Tweet', $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExchangeArrayException()
    {
        $data = 'some data';

        $tweet = new Tweet();
        $tweet->exchangeArray($data);
    }

}


class myClass extends Tweet
{
    public function setId($id)
    {
        // some bad coding
        $this->id = $id;
    }

    public function setText($text)
    {
        // no validation here
        $this->text = $text;
    }
}
