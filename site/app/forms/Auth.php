<?php declare(strict_types=1);

namespace app\forms;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class Auth extends \Phalcon\Forms\Form
{
    /**
     * This method returns the default value for field 'csrf'
     */
    public function getCsrf(): string
    {
        return $this->security->getToken();
    }

    public function initialize()
    {
        $login = new Text('login', ['required' => true]);
        $login->addValidator(new PresenceOf(array(
            'message' => 'Login is required'
        )));
        $this->add($login);
        $password = new Text('password', ['required' => true]);
        $password->addValidator(new PresenceOf(array(
            'message' => 'Password is required'
        )));
        $this->add($password);
        $csrf = new Hidden('csrf', ['value' => $this->security->getToken()]);
        $csrf->addValidator(new PresenceOf(array(
            'message' => 'Csrf is required'
        )));
        $csrf->addValidator(
            new Identical([
                'value' => $this->security->checkToken('csrf', $this->getValue('csrf')),
                'message' => 'CSRF validation failed'
            ])
        );
        $this->add($csrf);
    }
}
