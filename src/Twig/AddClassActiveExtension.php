<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddClassActiveExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('isActive', array($this, 'check'))
        );
    }

    public function check($routesToCheck)
    {
        $currentRoute = $this->request->getMasterRequest()->get('_route');

       // dump($this->request->getMasterRequest());
        
        if ($routesToCheck == $currentRoute) {

            if ($this->request->getMasterRequest()->query->has('s'))
                return $this->request->getMasterRequest()->query->get('s');
            elseif ($this->request->getMasterRequest()->attributes->has('sub_category_slug'))
                return $this->request->getMasterRequest()->attributes->get('sub_category_slug');

            return true;
        }

        return false;
    }
}