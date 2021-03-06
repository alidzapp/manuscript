<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Application',
            'basePath'  => APPLICATION_PATH));
        return $moduleLoader;
    }

    protected function _initViewHelpers()
    {
        $this->bootstrap('layout');
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addHelperPath(APPLICATION_PATH . '/layouts/helpers');
    }

    protected function _initFCPlugins()
    {
        $this->bootstrap('frontController');
        $this->bootstrap('RegisterNamespaces');

        $fc = $this->getResource('frontController');

        $fc->registerPlugin( new Manuscript_Controller_Plugin_Auth() );
        $fc->registerPlugin( new Tws_Controller_Plugin_ModuleLayout() );

        $config = $this->getApplication()->getOptions();
        if(!array_key_exists('installed', $config)) {
           $fc->registerPlugin( new Manuscript_Controller_Plugin_RunInstall() );
        } else {
            $this->bootstrap('db');
            $db = $this->getResource('db');
            $fc->registerPlugin( new Manuscript_Controller_Plugin_CheckDBDeltas($config['delta_version'], $db) );
        }

        return $fc;
    }

    protected function _initRegisterNamespaces()
    {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Manuscript_');
        $loader->registerNamespace('Tws_');
    }
}

