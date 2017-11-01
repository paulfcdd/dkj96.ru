<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="feedback")
 * @ORM\HasLifecycleCallbacks()
 */
class Feedback extends NotificationClass
{

	const TO_WHOM = [
		'director' => 'Директор',
		'client_review' => 'Оставить отзыв',
		'client_question' => 'Задать вопрос ',
		'client_comm_propsal' => 'Коммерческое предложение',
		'administrator' => 'Администрация'
	];

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    protected $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
    protected $toWhom;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Feedback
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return string
	 */
	public function getToWhom()
	{
		return $this->toWhom;
	}

	/**
	 * @param string $toWhom
	 * @return Feedback
	 */
	public function setToWhom(string $toWhom)
	{
		$this->toWhom = $toWhom;

		return $this;
	}


}
