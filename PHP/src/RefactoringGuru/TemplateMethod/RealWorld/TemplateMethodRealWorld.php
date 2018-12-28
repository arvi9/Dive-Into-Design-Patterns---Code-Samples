<?php

namespace RefactoringGuru\TemplateMethod\RealWorld;

/**
 * Template Method Design Pattern
 *
 * Intent: Define the skeleton of an algorithm in operation, deferring
 * implementation of some steps to subclasses. Template Method lets subclasses
 * redefine specific steps of an algorithm without changing the algorithm's
 * structure.
 *
 * Example: In this example, the Template Method defines a skeleton of the
 * algorithm of message posting to social networks. Each subclass represents a
 * separate social network and implements all the steps differently, but reuses
 * the base algorithm.
 */

/**
 * The Abstract Class defines the template method and declares all its steps.
 */
abstract class SocialNetwork
{
    protected $username;

    protected $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * The actual template method calls abstract steps in a specific order. A
     * subclass may implement all of the steps, allowing this method to actually
     * post something to a social network.
     */
    public function post(string $message): bool
    {
        // Authenticate before posting. Every network uses a different
        // authentication method.
        if ($this->logIn($this->username, $this->password)) {
            // Send the post data. All networks have different APIs.
            $result = $this->sendData($message);
            // ...
            $this->logOut();

            return $result;
        }

        return false;
    }

    /**
     * The steps are declared abstract to force the subclasses to implement them
     * all.
     */
    public abstract function logIn(string $userName, string $password): bool;

    public abstract function sendData(string $message): bool;

    public abstract function logOut(): void;
}

/**
 * This Concrete Class implements the Facebook API (all right, it pretends to).
 */
class Facebook extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        echo "\nChecking user's credentials...\n";
        echo "Name: ".$this->username."\n";
        echo "Password: ".str_repeat("*", strlen($this->password))."\n";

        simulateNetworkLatency();

        echo "\n\nFacebook: '".$this->username."' has logged in successfully.\n";

        return true;
    }

    public function sendData(string $message): bool
    {
        echo "Facebook: '".$this->username."' has posted '".$message."'.\n";

        return true;
    }

    public function logOut(): void
    {
        echo "Facebook: '".$this->username."' has been logged out.\n";
    }
}

/**
 * This Concrete Class implements the Twitter API.
 */
class Twitter extends SocialNetwork
{
    public function logIn(string $userName, string $password): bool
    {
        echo "\nChecking user's credentials...\n";
        echo "Name: ".$this->username."\n";
        echo "Password: ".str_repeat("*", strlen($this->password))."\n";

        simulateNetworkLatency();

        echo "\n\nTwitter: '".$this->username."' has logged in successfully.\n";

        return true;
    }

    public function sendData(string $message): bool
    {
        echo "Twitter: '".$this->username."' has posted '".$message."'.\n";

        return true;
    }

    public function logOut(): void
    {
        echo "Twitter: '".$this->username."' has been logged out.\n";
    }
}

/**
 * A little helper function that makes waiting times feel real.
 */
function simulateNetworkLatency()
{
    $i = 0;
    while ($i < 5) {
        echo ".";
        sleep(1);
        $i++;
    }
}

/**
 * The client code.
 */
echo "Username: \n";
$username = readline();
echo "Password: \n";
$password = readline();
echo "Message: \n";
$message = readline();

echo "\nChoose the social network to post the message:\n".
    "1 - Facebook\n".
    "2 - Twitter\n";
$choice = readline();

// Now, let's create a proper social network object and send the message.
if ($choice == 1) {
    $network = new Facebook($username, $password);
} elseif ($choice == 2) {
    $network = new Twitter($username, $password);
} else {
    die("Sorry, I'm not sure what you mean by that.\n");
}
$network->post($message);
