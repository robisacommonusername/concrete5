<?php
namespace Concrete\Core\Application;

/**
 * Interface ApplicationAwareInterface
 * This interface declares awareness of the concrete5 application.
 *
 * @package Concrete\Core\Application
 */
interface ApplicationAwareInterface
{

    /**
     * Set the application object
     *
     * @param \Illuminate\Container\Container $application
     */
    public function setApplication(\Illuminate\Container\Container $application);

}
