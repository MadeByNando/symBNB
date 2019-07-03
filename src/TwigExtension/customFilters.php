<?php
// The customFilters class extends Twig Extension which gives us access to the getFilters() method
// The getFilters() method returns an array of custom filters 
// Each item in this array is an instance of the TwigFilter class
// While instantiating the TwigFilter class, we pass it 2 parameters
// The name of the twig filter
// An array that contains the name of the method that should be called while using the custom filter
// Example: new TwigFilter('base64_encode', array($this, 'base64_en')). Here, base64_encode is the name of our custom filter, and base64_en is the name of the method to be called.

// Write the body of the method that you provided in the previous step (base64_en). The method will automatically get a parameter (we’ve named it $input). For example: public function base64_en($input) { ... } .
// The contents of this method could be whatever you intend to do with the passed input. We have just called php’s base64_encode() on the input.

namespace App\TwigExtension;

use Twig\TwigFilter;

class customFilters extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('base64_encode', array($this, 'base64_en')), // base64_encode => name of custom filter, base64_en => name of function to execute when this filter is called.
            new TwigFilter('base64_decode', array($this, 'base64_dec'))
        );
    }

    public function base64_en($input)
    {
        return base64_encode($input);
    }

    public function base64_dec($input)
    {
        return base64_decode($input);
    }
}
