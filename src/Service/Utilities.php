<?php  

namespace App\Service;

class Utilities
{
    /**
     * Permet de retourner un hello
     *
     * @param string $name
     * 
     * @return string
     */
    public function sayHello($name)
    {
        return "Hello $name !";
    }
}

?>