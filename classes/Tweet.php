<?php

class Tweet
{

    protected $id;
    protected $text;

    /**
     * Get Tweet Id
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public function getId()
    {
        if (!is_numeric($this->id)) {
            throw new \InvalidArgumentException('Tweet Id must be numeric');
        }

        return $this->id;
    }

    /**
     * Set Tweet Id
     *
     * @param mixed $id
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setId($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Tweet Id must be numeric');
        }

        $this->id = $id;
        return $this;
    }

    /**
     * Get Tweet Text
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function getText()
    {
        if (!is_string($this->text)) {
            throw new \InvalidArgumentException('Tweet text must be a string');
        }

        return $this->text;
    }

    /**
     * Set Tweet Text
     *
     * @param mixed $text
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new \InvalidArgumentException('Tweet text must be a string');
        }

        $this->text = $text;
        return $this;
    }

    /**
     * Exchange Tweet data array
     *
     * @param stdClass $data Data Array
     * @return $this
     */
    public function exchangeArray($data)
    {
        if (!($data instanceof stdClass)) {
            throw new InvalidArgumentException('Tweet data has to be stdClass');
        }

        $this->setId($data->id);
        $this->setText($data->text);

        return $this;
    }

}
