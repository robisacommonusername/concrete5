<?php
namespace Concrete\Core\Application;

trait ApplicationAwareTrait
{

    /**
     * @type \Illuminate\Container\Container
     * @read-only
     */
    private $_application;

    /**
     * Get the application object
     *
     * @return \Illuminate\Container\Container
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Set the application object
     *
     * @param \Illuminate\Container\Container $application
     */
    public function setApplication(\Illuminate\Container\Container $application)
    {
        $this->_application = $application;
    }

}
