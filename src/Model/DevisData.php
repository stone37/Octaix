<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class DevisData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     *  @Assert\NotBlank()
     */
    public $phone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="10")
     */
    public $content;

    /**
     * @Assert\NotBlank()
     */
    public $status;

    /**
     * @Assert\NotBlank()
     */
    public $budget;

    /**
     * @Assert\NotBlank()
     */
    public $project;

    /**
     * @Assert\NotBlank()
     */
    public $delay;
}
